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
        // Development / assessment examples, NOT verified dealer offers.
        $examples = [
            ['honda', 'Honda Autumn Offer (Demo)', 'عرض هوندا الخريفي (تجريبي)',
                'Illustrative discount on a selected Honda model.', 'خصم تجريبي على موديل مختار من هوندا.', 7.5, true],
            ['yamaha', 'Yamaha City Ride (Demo)', 'عرض ياماها لرحلات المدينة (تجريبي)',
                'Illustrative seasonal promotion for Yamaha motorcycles.', 'عرض موسمي تجريبي على دراجات ياماها.', 5, false],
            ['kawasaki', 'Kawasaki Performance Days (Demo)', 'أيام كاواساكي للأداء (تجريبي)',
                'Illustrative promotion on a selected Kawasaki model.', 'عرض تجريبي على موديل مختار من كاواساكي.', 8, true],
            ['suzuki', 'Suzuki Road Adventure (Demo)', 'مغامرات الطريق من سوزوكي (تجريبي)',
                'Illustrative offer across Suzuki motorcycles.', 'عرض تجريبي على دراجات سوزوكي النارية.', 6, false],
            ['bmw-motorrad', 'BMW Motorrad Touring (Demo)', 'عرض بي إم دبليو للرحلات (تجريبي)',
                'Illustrative premium touring discount.', 'خصم تجريبي على دراجات الرحلات الفاخرة.', 4, true],
            ['ducati', 'Ducati Weekend (Demo)', 'عطلة دوكاتي (تجريبي)',
                'Illustrative weekend promotion for Ducati motorcycles.', 'عرض نهاية أسبوع تجريبي لدراجات دوكاتي.', 5, false],
        ];

        $from = now()->startOfMonth()->toDateString();
        $until = now()->addMonths(3)->endOfMonth()->toDateString();

        foreach ($examples as $index => [$slug, $titleEn, $titleAr, $descEn, $descAr, $discount, $targetModel]) {
            $brand = Brand::query()->where('slug', $slug)->first();

            if (! $brand) {
                continue;
            }

            $motorcycle = $targetModel
                ? Motorcycle::query()->where('brand_id', $brand->id)->orderBy('id')->first()
                : null;

            Promotion::query()->firstOrCreate(
                ['brand_id' => $brand->id, 'title_en' => $titleEn],
                [
                    'motorcycle_id' => $motorcycle?->id,
                    'title_ar' => $titleAr,
                    'description_en' => $descEn,
                    'description_ar' => $descAr,
                    'discount_percent' => $discount,
                    'starts_at' => $from,
                    'ends_at' => $until,
                    'image_path' => null,
                    'is_active' => false, // Demo only: admin can publish after approval.
                    'sort_order' => $index + 1,
                ],
            );
        }
    }
}
