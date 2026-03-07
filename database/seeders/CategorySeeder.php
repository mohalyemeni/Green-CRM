<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clothes = Category::create(['name' => 'ملابس', 'cover' => 'colthes.jpg', 'status' => true, 'parent_id' => null]);
        Category::create(['name' => 'تيشيرتات نسائية', 'cover' => 'colthes.jpg', 'status' => true, 'parent_id' => $clothes->id]);
        Category::create(['name' => 'تيشيرتات رجالية', 'cover' => 'colthes.jpg', 'status' => true, 'parent_id' => $clothes->id]);
        Category::create(['name' => 'فساتين', 'cover' => 'colthes.jpg', 'status' => true, 'parent_id' => $clothes->id]);
        Category::create(['name' => 'جوارب متنوعة', 'cover' => 'colthes.jpg', 'status' => true, 'parent_id' => $clothes->id]);
        Category::create(['name' => 'نظارات شمسية نسائية', 'cover' => 'colthes.jpg', 'status' => true, 'parent_id' => $clothes->id]);
        Category::create(['name' => 'نظارات شمسية رجالية', 'cover' => 'colthes.jpg', 'status' => true, 'parent_id' => $clothes->id]);


        $shoes = Category::create(['name' => 'أحذية', 'cover' => 'shose.jpg', 'status' => true, 'parent_id' => null]);
        Category::create(['name' => 'أحذية نسائية', 'cover' => 'shose.jpg', 'status' => true, 'parent_id' => $shoes->id]);
        Category::create(['name' => 'أحذية رجالية', 'cover' => 'shose.jpg', 'status' => true, 'parent_id' => $shoes->id]);
        Category::create(['name' => 'أحذية أولاد', 'cover' => 'shose.jpg', 'status' => true, 'parent_id' => $shoes->id]);
        Category::create(['name' => 'أحذية بنات', 'cover' => 'shose.jpg', 'status' => true, 'parent_id' => $shoes->id]);


        $watches = Category::create(['name' => 'ساعات', 'cover' => 'watches.jpg', 'status' => true, 'parent_id' => null]);
        Category::create(['name' => 'ساعات نسائية', 'cover' => 'watches.jpg', 'status' => true, 'parent_id' => $watches->id]);
        Category::create(['name' => 'ساعات رجالية', 'cover' => 'watches.jpg', 'status' => true, 'parent_id' => $watches->id]);
        Category::create(['name' => 'ساعات أولاد', 'cover' => 'watches.jpg', 'status' => true, 'parent_id' => $watches->id]);
        Category::create(['name' => 'ساعات بنات', 'cover' => 'watches.jpg', 'status' => true, 'parent_id' => $watches->id]);


        $electronics = Category::create(['name' => 'إلكترونيات', 'cover' => 'electronies.jpg', 'status' => true, 'parent_id' => null]);
        Category::create(['name' => 'إلكترونيات نسائية', 'cover' => 'electronies.jpg', 'status' => true, 'parent_id' => $electronics->id]);
        Category::create(['name' => 'إلكترونيات رجالية', 'cover' => 'electronies.jpg', 'status' => true, 'parent_id' => $electronics->id]);
        Category::create(['name' => 'إلكترونيات أولاد', 'cover' => 'electronies.jpg', 'status' => true, 'parent_id' => $electronics->id]);
        Category::create(['name' => 'إلكترونيات بنات', 'cover' => 'electronies.jpg', 'status' => true, 'parent_id' => $electronics->id]);
    }
}
