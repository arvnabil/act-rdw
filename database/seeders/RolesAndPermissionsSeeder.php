<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define permissions for each module/resource
        $permissions = [
            // News
            'view_any_news', 'view_news', 'create_news', 'update_news', 'delete_news', 'delete_any_news',
            'view_any_news_category', 'view_news_category', 'create_news_category', 'update_news_category', 'delete_news_category',
            'view_any_news_tag', 'view_news_tag', 'create_news_tag', 'update_news_tag', 'delete_news_tag',

            // Projects
            'view_any_project', 'view_project', 'create_project', 'update_project', 'delete_project', 'delete_any_project',

            // CMS / Pages
            'view_any_page', 'view_page', 'create_page', 'update_page', 'delete_page', 'delete_any_page',

            // Services
            'view_any_service', 'view_service', 'create_service', 'update_service', 'delete_service', 'delete_any_service',

            // Product Catalog
            'view_any_product', 'view_product', 'create_product', 'update_product', 'delete_product', 'delete_any_product',
            'view_any_brand', 'view_brand', 'create_brand', 'update_brand', 'delete_brand',

            // Clients
            'view_any_client', 'view_client', 'create_client', 'update_client', 'delete_client', 'delete_any_client',

            // Events
            'view_any_event', 'view_event', 'create_event', 'update_event', 'delete_event', 'delete_any_event',

            // Campaign
            'view_any_campaign', 'view_campaign', 'create_campaign', 'update_campaign', 'delete_campaign',

            // Analytics & SEO
            'view_analytics', 'view_seo', 'update_seo',

            // Settings
            'view_settings', 'update_settings',

            // Menu
            'view_any_menu', 'view_menu', 'create_menu', 'update_menu', 'delete_menu',

            // Form Builder
            'view_any_form', 'view_form', 'create_form', 'update_form', 'delete_form',

            // Search
            'view_search', 'update_search',

            // WhatsApp
            'view_whatsapp', 'update_whatsapp',

            // User Management (admin only)
            'view_any_user', 'view_user', 'create_user', 'update_user', 'delete_user', 'delete_any_user',
            'view_any_role', 'view_role', 'create_role', 'update_role', 'delete_role',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // =============================================
        // ROLE: Administrator (full access)
        // =============================================
        $adminRole = Role::firstOrCreate(['name' => 'administrator']);
        $adminRole->syncPermissions(Permission::all());

        // =============================================
        // ROLE: Co-Admin (manage own content)
        // =============================================
        $coAdminRole = Role::firstOrCreate(['name' => 'co-admin']);
        $coAdminRole->syncPermissions([
            // News - own data only (enforced in Resource via query scope)
            'view_any_news', 'view_news', 'create_news', 'update_news', 'delete_news',
            'view_any_news_category', 'view_news_category', 'create_news_category', 'update_news_category',
            'view_any_news_tag', 'view_news_tag', 'create_news_tag', 'update_news_tag',

            // Projects - own data only
            'view_any_project', 'view_project', 'create_project', 'update_project', 'delete_project',

            // CMS Pages - own data only
            'view_any_page', 'view_page', 'create_page', 'update_page',

            // Services
            'view_any_service', 'view_service', 'create_service', 'update_service',

            // Products
            'view_any_product', 'view_product', 'create_product', 'update_product',
            'view_any_brand', 'view_brand', 'create_brand', 'update_brand',

            // Clients
            'view_any_client', 'view_client', 'create_client', 'update_client',

            // Events
            'view_any_event', 'view_event', 'create_event', 'update_event',
        ]);

        // =============================================
        // ROLE: Editor (content only)
        // =============================================
        $editorRole = Role::firstOrCreate(['name' => 'editor']);
        $editorRole->syncPermissions([
            'view_any_news', 'view_news', 'create_news', 'update_news',
            'view_any_news_category', 'view_news_category',
            'view_any_news_tag', 'view_news_tag',
            'view_any_project', 'view_project', 'create_project', 'update_project',
            'view_any_page', 'view_page', 'update_page',
        ]);

        // =============================================
        // ROLE: Viewer (read only)
        // =============================================
        $viewerRole = Role::firstOrCreate(['name' => 'viewer']);
        $viewerRole->syncPermissions([
            'view_any_news', 'view_news',
            'view_any_project', 'view_project',
            'view_any_page', 'view_page',
            'view_analytics',
        ]);

        // Create default admin user if not exists
        $admin = User::firstOrCreate(
            ['email' => 'admin@activoncms.com'],
            [
                'name' => 'Super Administrator',
                'password' => bcrypt('password'),
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('administrator');

        // Assign administrator role to nabil@activ.co.id if exists
        $nabil = User::where('email', 'nabil@activ.co.id')->first();
        if ($nabil) {
            $nabil->assignRole('administrator');
        }

        $this->command->info('Roles & Permissions seeded successfully!');
        $this->command->table(
            ['Role', 'Permissions Count'],
            [
                ['administrator', $adminRole->permissions()->count()],
                ['co-admin', $coAdminRole->permissions()->count()],
                ['editor', $editorRole->permissions()->count()],
                ['viewer', $viewerRole->permissions()->count()],
            ]
        );
    }
}
