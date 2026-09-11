<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            'تراث شفهي',
            'موسيقى فلكلورية',
            'أزياء تقليدية',
            'حرف يدوية',
            'مأكولات شعبية',
            'طقوس ومناسبات',
        ];

        foreach ($items as $name) {
            Category::firstOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'description' => null,
                ]
            );
        }
    }
}