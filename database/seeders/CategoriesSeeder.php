<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        Category::truncate(); 
        Schema::enableForeignKeyConstraints();

        $categories = [
            [
                'name' => ['en' => 'Masonry', 'ar' => 'أعمال البناء'],
                'description' => ['en' => 'Construction and bricklaying work', 'ar' => 'أعمال البناء والطوب'],
            ],
            [
                'name' => ['en' => 'Electrical Work', 'ar' => 'الأعمال الكهربائية'],
                'description' => ['en' => 'Installation and maintenance of electrical systems', 'ar' => 'تركيب وصيانة الأنظمة الكهربائية'],
            ],
            [
                'name' => ['en' => 'Plumbing', 'ar' => 'السباكة'],
                'description' => ['en' => 'Water and drainage system installation', 'ar' => 'تركيب أنظمة المياه والصرف الصحي'],
            ],
            [
                'name' => ['en' => 'Painting & Finishing', 'ar' => 'الطلاء والتشطيبات'],
                'description' => ['en' => 'Interior and exterior painting', 'ar' => 'طلاء داخلي وخارجي'],
            ],
            [
                'name' => ['en' => 'Carpentry', 'ar' => 'النجارة'],
                'description' => ['en' => 'Woodwork and furniture installation', 'ar' => 'أعمال الخشب وتركيب الأثاث'],
            ],
            [
                'name' => ['en' => 'Roofing', 'ar' => 'تغطية الأسقف'],
                'description' => ['en' => 'Roof installation and waterproofing', 'ar' => 'تركيب الأسطح والعزل المائي'],
            ],
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category['name'],
                'description' => $category['description'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
