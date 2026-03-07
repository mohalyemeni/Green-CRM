<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         // تفرغ الجدول
        // DB::table('permissions')->truncate();

        $admin = Role::whereName('admin')->first();

        //permission to manage main dashboard page
        $manageMain = Permission::create([
            'name'=>'Main',
            'display_name'=>'Main',
            'route'=>'index',
            'module'=>'',
            'as'=>'index',
            'icon'=>'home',
            'parent'=>'0',
            'parent_original'=>'0',
            'sidebar_link'=>'1',
            'appear'=>'1',
            'ordering'=>'100'
        ]);
        $manageMain->parent_show = $manageMain->id;
        $manageMain->save();
        $admin->attachPermission($manageMain);
        // $user = User::find(1);  // استرجاع المستخدم
        // $user->attachPermission($manageMain);  // ربط الإذن بالاسم

         //permissions of product categories
         // 'as' => 'manage_category' mean witch function in the controller work with this permition roles
         // 'parent' => $manageCategories->id means this permition is son of manageproductcategories
         // 'parent_original' => $manageCategories->id means this permistion will be shown under the father permistion link
         // 'appear' => 1 means will appear in dashboard

         $manageCategories = Permission::create([
            'name' => 'manage-category',
            'display_name' => 'Categories',
            'route' => 'index',
            'module' => 'category',
            'as' => 'category.index',
            'icon' => 'grid' ,
            'parent' => '0' ,
            'parent_original' => '0',
            'sidebar_link' => '1',
            'appear' => '1' ,
            'ordering' => '200'
        ]);
         $manageCategories->parent_show = $manageCategories->id;
         $manageCategories->save();

         $viewCategories    =  Permission::create([
            'name' => 'view-category',
            'display_name' => 'View Categories',
            'route' => 'index',
            'module' => 'category',
            'as' => 'category.index',
            'icon' => 'folder-plus',
            'parent' => $manageCategories->id,
            'parent_original' => $manageCategories->id,
            'parent_show' => $manageCategories->id,
            'sidebar_link' => '1',
            'appear' => '1'
         ] );
         $createCategories  =  Permission::create([
            'name' => 'create-category',
            'display_name'  => 'Create Category',
            'route' => 'create',
            'module' => 'category',
            'as' => 'category.create',
            'icon' => 'plus-square',
            'parent' => $manageCategories->id,
            'parent_original' => $manageCategories->id,
            'parent_show' => $manageCategories->id,
            'sidebar_link' => '1',
            'appear' => '0'
        ] );
         $showCategories =  Permission::create([
            'name' => 'show-category',
            'display_name'  => 'Show Category',
            'route' => 'show',
            'module' => 'category',
            'as' => 'category.show',
            'icon' => 'edit',
            'parent' => $manageCategories->id,
            'parent_original' => $manageCategories->id,
            'parent_show' => $manageCategories->id,
            'sidebar_link' => '0',
            'appear' => '0'
        ] );

        $editCategories =  Permission::create([
            'name' => 'edit-category',
            'display_name'  => 'Edit Category',
            'route' => 'edit',
            'module' => 'category',
            'as' => 'category.edit',
            'icon' => 'edit',
            'parent' => $manageCategories->id,
            'parent_original' => $manageCategories->id,
            'parent_show' => $manageCategories->id,
            'sidebar_link' => '0',
            'appear' => '0'
        ] );
         $updateCategories  =  Permission::create([
            'name' => 'update-category',
            'display_name'  => 'Update Category',
            'route' => 'category',
            'module' => 'category',
            'as' => 'category.update',
            'icon' => null,
            'parent' => $manageCategories->id,
            'parent_original' => $manageCategories->id,
            'parent_show' => $manageCategories->id,
            'sidebar_link' => '0',
            'appear' => '0'
        ] );
         $deleteCategories  =  Permission::create([
            'name' => 'delete-category',
            'display_name'  => 'Delete Category',
            'route' => 'destroy',
            'module' => 'category',
            'as' => 'category.destroy',
            'icon' => 'trash-2',
            'parent' => $manageCategories->id,
            'parent_original' => $manageCategories->id,
            'parent_show' => $manageCategories->id,
            'sidebar_link' => '0',
            'appear' => '0'
        ] );

    }

}
