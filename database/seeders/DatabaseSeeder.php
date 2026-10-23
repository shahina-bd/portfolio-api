<?php

namespace Database\Seeders;

use App\Models\PersonalInfo;
use App\Models\Project;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Admin User
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
            ]
        );

        // 2. Personal Information
        PersonalInfo::updateOrCreate(
            ['email' => 'developer@example.com'],
            [
                'name' => 'Alex Morgan',
                'title' => 'Senior Full Stack Developer',
                'bio' => 'Passionate software engineer specialized in building scalable web applications with Laravel, Vue, and modern cloud technologies.',
                'phone' => '+1 (555) 123-4567',
                'location' => 'San Francisco, CA',
                'avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=400',
                'resume_url' => 'https://example.com/resume.pdf',
                'github_url' => 'https://github.com',
                'linkedin_url' => 'https://linkedin.com',
                'social_links' => [
                    'twitter' => 'https://x.com',
                    'github' => 'https://github.com',
                    'linkedin' => 'https://linkedin.com',
                ],
            ]
        );

        // 3. Technical Skills
        $skills = [
            ['name' => 'PHP', 'category' => 'Backend', 'icon' => 'devicon-php-plain', 'level' => 'Expert'],
            ['name' => 'Laravel', 'category' => 'Backend', 'icon' => 'devicon-laravel-plain', 'level' => 'Expert'],
            ['name' => 'MySQL', 'category' => 'Database', 'icon' => 'devicon-mysql-plain', 'level' => 'Advanced'],
            ['name' => 'JavaScript', 'category' => 'Frontend', 'icon' => 'devicon-javascript-plain', 'level' => 'Advanced'],
            ['name' => 'Vue.js', 'category' => 'Frontend', 'icon' => 'devicon-vuejs-plain', 'level' => 'Advanced'],
            ['name' => 'Tailwind CSS', 'category' => 'Frontend', 'icon' => 'devicon-tailwindcss-plain', 'level' => 'Expert'],
            ['name' => 'Docker', 'category' => 'DevOps', 'icon' => 'devicon-docker-plain', 'level' => 'Intermediate'],
            ['name' => 'Git', 'category' => 'Tools', 'icon' => 'devicon-git-plain', 'level' => 'Advanced'],
        ];

        foreach ($skills as $skill) {
            Skill::updateOrCreate(['name' => $skill['name']], $skill);
        }

        // 4. Sample Projects
        $projects = [
            [
                'title' => 'E-Commerce Platform',
                'slug' => 'e-commerce-platform',
                'description' => 'A full-featured e-commerce API and store with payment processing, inventory tracking, and customer management.',
                'image' => 'https://images.unsplash.com/photo-1557821552-17105176677c?w=800',
                'technologies' => ['Laravel', 'Vue.js', 'MySQL', 'Stripe'],
                'github_url' => 'https://github.com/example/ecommerce-platform',
                'live_url' => 'https://ecommerce.example.com',
                'featured' => true,
            ],
            [
                'title' => 'Task Management System',
                'slug' => 'task-management-system',
                'description' => 'Collaborative project management dashboard supporting real-time team updates and automated notifications.',
                'image' => 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?w=800',
                'technologies' => ['Laravel', 'Tailwind CSS', 'Pusher', 'MySQL'],
                'github_url' => 'https://github.com/example/task-manager',
                'live_url' => 'https://tasks.example.com',
                'featured' => true,
            ],
            [
                'title' => 'Developer Portfolio API',
                'slug' => 'developer-portfolio-api',
                'description' => 'Headless RESTful API powering a dynamic developer portfolio with Sanctum token authentication.',
                'image' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=800',
                'technologies' => ['Laravel', 'PHP', 'Sanctum', 'REST API'],
                'github_url' => 'https://github.com/example/portfolio-api',
                'live_url' => 'https://api.example.com',
                'featured' => false,
            ],
        ];

        foreach ($projects as $project) {
            Project::updateOrCreate(['slug' => $project['slug']], $project);
        }
    }
}
