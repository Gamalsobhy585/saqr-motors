<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Motorcycle;
use App\Models\Promotion;
use Illuminate\Database\Seeder;

class PromotionSeeder extends Seeder
{
    public function run(): void
    {
        // Assessment-only offers: not verified dealer promotions.
        // Active offers target published motorcycles so pricing can be demonstrated.
        $examples = [
            [
                'brand' => 'honda',
                'motorcycle_slug' => 'cb650r',
                'title_en' => 'Honda Autumn Offer (Demo)',
                'title_ar' => 'عرض هوندا الخريفي (تجريبي)',
                'description_en' => 'Illustrative discount on a selected Honda model.',
                'description_ar' => 'خصم تجريبي على موديل مختار من هوندا.',
                'discount' => 7.5,
                'active' => true,
            ],
            [
                'brand' => 'yamaha',
                'motorcycle_slug' => null, // Brand-wide offer.
                'title_en' => 'Yamaha City Ride (Demo)',
                'title_ar' => 'عرض ياماها لرحلات المدينة (تجريبي)',
                'description_en' => 'Illustrative seasonal promotion for Yamaha motorcycles.',
                'description_ar' => 'عرض موسمي تجريبي على دراجات ياماها.',
                'discount' => 5,
                'active' => true,
            ],
            [
                'brand' => 'kawasaki',
                'motorcycle_slug' => 'ninja-650',
                'title_en' => 'Kawasaki Performance Days (Demo)',
                'title_ar' => 'أيام كاواساكي للأداء (تجريبي)',
                'description_en' => 'Illustrative promotion on a selected Kawasaki model.',
                'description_ar' => 'عرض تجريبي على موديل مختار من كاواساكي.',
                'discount' => 8,
                'active' => true,
            ],
            [
                'brand' => 'suzuki',
                'motorcycle_slug' => null,
                'title_en' => 'Suzuki Road Adventure (Demo)',
                'title_ar' => 'مغامرات الطريق من سوزوكي (تجريبي)',
                'description_en' => 'Illustrative offer across Suzuki motorcycles.',
                'description_ar' => 'عرض تجريبي على دراجات سوزوكي النارية.',
                'discount' => 6,
                'active' => false,
            ],
            [
                'brand' => 'ducati',
                'motorcycle_slug' => null,
                'title_en' => 'Ducati Weekend (Demo)',
                'title_ar' => 'عطلة دوكاتي (تجريبي)',
                'description_en' => 'Illustrative weekend promotion for Ducati motorcycles.',
                'description_ar' => 'عرض نهاية أسبوع تجريبي لدراجات دوكاتي.',
                'discount' => 5,
                'active' => false,
            ],
        ];

        $from = now()->startOfMonth()->toDateString();
        $until = now()->addMonths(3)->endOfMonth()->toDateString();

        foreach ($examples as $index => $example) {
            $brand = Brand::query()->where('slug', $example['brand'])->first();

            if (! $brand) {
                $this->command?->warn("Missing brand '{$example['brand']}' for promotion.");
                continue;
            }

            $motorcycle = null;

            if ($example['motorcycle_slug'] !== null) {
                $motorcycle = Motorcycle::query()
                    ->where('brand_id', $brand->id)
                    ->where('slug', $example['motorcycle_slug'])
                    ->first();

                if (! $motorcycle) {
                    $this->command?->warn("Missing motorcycle '{$example['motorcycle_slug']}' for promotion.");
                    continue;
                }
            }

            Promotion::query()->firstOrCreate(
                [
                    'brand_id' => $brand->id,
                    'title_en' => $example['title_en'],
                ],
                [
                    'motorcycle_id' => $motorcycle?->id,
                    'title_ar' => $example['title_ar'],
                    'description_en' => $example['description_en'],
                    'description_ar' => $example['description_ar'],
                    'discount_percent' => $example['discount'],
                    'starts_at' => $from,
                    'ends_at' => $until,
                    'image_path' => null,
                    'is_active' => $example['active'],
                    'sort_order' => $index + 1,
                ]
            );
        }
    }
}
