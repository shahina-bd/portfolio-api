<?php

namespace Tests\Feature;

use App\Models\PersonalInfo;
use App\Models\Project;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortfolioApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_retrieve_profile(): void
    {
        PersonalInfo::create([
            'name' => 'Alex Morgan',
            'title' => 'Software Engineer',
            'bio' => 'Building scalable web applications.',
            'email' => 'alex@example.com',
        ]);

        $response = $this->getJson('/api/profile');

        $response->assertStatus(200)
            ->assertJsonPath('name', 'Alex Morgan')
            ->assertJsonPath('title', 'Software Engineer');
    }

    public function test_can_retrieve_all_projects(): void
    {
        Project::create([
            'title' => 'Project Alpha',
            'slug' => 'project-alpha',
            'description' => 'Alpha description',
            'technologies' => ['PHP', 'Laravel'],
            'featured' => true,
        ]);

        $response = $this->getJson('/api/projects');

        $response->assertStatus(200)
            ->assertJsonCount(1);
    }

    public function test_can_retrieve_single_project_by_id_and_slug(): void
    {
        $project = Project::create([
            'title' => 'Project Beta',
            'slug' => 'project-beta',
            'description' => 'Beta description',
            'technologies' => ['Vue.js'],
            'featured' => false,
        ]);

        // By ID
        $response = $this->getJson("/api/projects/{$project->id}");
        $response->assertStatus(200)->assertJsonPath('title', 'Project Beta');

        // By Slug
        $responseSlug = $this->getJson("/api/projects/project-beta");
        $responseSlug->assertStatus(200)->assertJsonPath('id', $project->id);

        // Not Found
        $responseNotFound = $this->getJson("/api/projects/non-existent");
        $responseNotFound->assertStatus(404);
    }

    public function test_can_retrieve_skills(): void
    {
        Skill::create([
            'name' => 'Laravel',
            'category' => 'Backend',
            'level' => 'Expert',
        ]);

        $response = $this->getJson('/api/skills');

        $response->assertStatus(200)
            ->assertJsonCount(1)
            ->assertJsonFragment(['name' => 'Laravel']);
    }

    public function test_can_submit_contact_message_with_validation(): void
    {
        // Validation failure (missing fields & invalid email)
        $invalidResponse = $this->postJson('/api/contact', [
            'name' => '',
            'email' => 'invalid-email',
            'message' => 'hi', // too short
        ]);
        $invalidResponse->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'message']);

        // Successful submission
        $validResponse = $this->postJson('/api/contact', [
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'subject' => 'Project Inquiry',
            'message' => 'Hello, I would like to discuss a freelance project opportunity with you.',
        ]);

        $validResponse->assertStatus(201)
            ->assertJsonPath('data.name', 'Jane Smith')
            ->assertJsonPath('data.is_read', false);

        $this->assertDatabaseHas('messages', [
            'email' => 'jane@example.com',
        ]);
    }

    public function test_admin_login_and_logout(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('password123'),
        ]);

        // Invalid credentials
        $failResponse = $this->postJson('/api/login', [
            'email' => 'admin@example.com',
            'password' => 'wrongpassword',
        ]);
        $failResponse->assertStatus(401);

        // Valid credentials
        $loginResponse = $this->postJson('/api/login', [
            'email' => 'admin@example.com',
            'password' => 'password123',
        ]);
        $loginResponse->assertStatus(200)
            ->assertJsonStructure(['message', 'user', 'token']);

        $token = $loginResponse->json('token');

        // Logout with token
        $logoutResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/logout');

        $logoutResponse->assertStatus(200)
            ->assertJsonPath('message', 'Logged out successfully.');
    }

    public function test_admin_project_crud_protected_by_auth(): void
    {
        $user = User::factory()->create();

        // 1. Unauthenticated attempts should be rejected with 401
        $this->postJson('/api/admin/projects', ['title' => 'Test'])->assertStatus(401);
        $this->putJson('/api/admin/projects/1', ['title' => 'Test'])->assertStatus(401);
        $this->deleteJson('/api/admin/projects/1')->assertStatus(401);

        // 2. Authenticated Admin creates project
        $token = $user->createToken('admin-token')->plainTextToken;
        $headers = ['Authorization' => 'Bearer ' . $token];

        $createResponse = $this->withHeaders($headers)->postJson('/api/admin/projects', [
            'title' => 'Admin Created Project',
            'description' => 'A project created via admin API',
            'technologies' => ['Laravel', 'Sanctum'],
            'github_url' => 'https://github.com/example/project',
            'live_url' => 'https://example.com',
            'featured' => true,
        ]);

        $createResponse->assertStatus(201)
            ->assertJsonPath('project.title', 'Admin Created Project')
            ->assertJsonPath('project.slug', 'admin-created-project');

        $projectId = $createResponse->json('project.id');

        // 3. Authenticated Admin updates project
        $updateResponse = $this->withHeaders($headers)->putJson("/api/admin/projects/{$projectId}", [
            'title' => 'Updated Project Title',
            'featured' => false,
        ]);

        $updateResponse->assertStatus(200)
            ->assertJsonPath('project.title', 'Updated Project Title')
            ->assertJsonPath('project.featured', false);

        // 4. Authenticated Admin deletes project
        $deleteResponse = $this->withHeaders($headers)->deleteJson("/api/admin/projects/{$projectId}");
        $deleteResponse->assertStatus(200);

        $this->assertDatabaseMissing('projects', ['id' => $projectId]);
    }
}
