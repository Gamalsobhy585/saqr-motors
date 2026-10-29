<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Location;
use App\Models\TestRideBooking;
use Illuminate\Database\Seeder;

class TestRideBookingSeeder extends Seeder
{
    public function run(): void
    {
        $brands = Brand::query()
            ->whereHas('motorcycles')
            ->with('motorcycles')
            ->get();

        if ($brands->isEmpty()) {
            $this->command?->warn('No brands with motorcycles found. Seed BrandSeeder and MotorcycleSeeder first.');

            return;
        }

        $locations = Location::query()->orderBy('id')->pluck('id')->all();

        // Fictional customer identities and numbers, for demonstration only.
        // Days are relative to seeding day, so Today/This Week remain useful.
        $samples = [
            ['محمد أحمد',      -8, 10,  0, 'أرغب في تجربة الدراجة قبل اتخاذ قرار الشراء.'],
            ['أحمد محمود',     -5, 13, 30, 'ممكن أعرف الأوراق المطلوبة لتجربة القيادة؟'],
            ['مريم السيد',     -3, 12,  0, 'أفضل تجربة القيادة في فرع قريب من مدينة نصر.'],
            ['كريم مصطفى',     -2, 16,  0, 'أريد تجربة الدراجة ومقارنة أكثر من موديل.'],
            ['سارة إبراهيم',   -1, 11, 30, 'هل يوجد خوذة ومعدات حماية أثناء التجربة؟'],
            ['يوسف خالد',       0, 10,  0, 'أرجو التواصل معي لتأكيد موعد تجربة القيادة.'],
            ['ندى محمد',        0, 13,  0, 'يفضل الاتصال بعد الساعة الخامسة مساءً.'],
            ['عمر عبد الرحمن',  0, 17, 30, 'أول مرة أجرب دراجة من هذه الفئة.'],
            ['ياسمين علي',      1, 11,  0, 'أود الاستفسار عن توافر المقاس المناسب.'],
            ['زياد طارق',       1, 15,  0, 'أحتاج معرفة شروط تجربة القيادة.'],
            ['هبة حسن',         2, 10, 30, 'هل يمكن تجربة الدراجة في عطلة نهاية الأسبوع؟'],
            ['عبد الله فؤاد',   2, 14,  0, 'أتمنى تحديد موعد مناسب للتجربة.'],
            ['فاطمة عادل',      4, 12,  0, 'أريد معرفة تفاصيل الموديل قبل الزيارة.'],
            ['رنا سمير',        6, 16,  0, 'أرغب في تجربة دراجة للرحلات الطويلة.'],
            ['محمود صلاح',      9, 13,  0, 'من فضلكم أكدوا لي مكان وموعد التجربة.'],
            ['نور خالد',       12, 17,  0, 'هل يمكن التواصل لتغيير موعد التجربة لاحقًا؟'],
        ];

        foreach ($samples as $index => [$name, $days, $hour, $minute, $notes]) {
            $brand = $brands[$index % $brands->count()];
            $motorcycle = $brand->motorcycles->first();
            $locationId = $locations === [] ? null : $locations[$index % count($locations)];

            TestRideBooking::query()->updateOrCreate(
                ['email' => sprintf('saqr-ride-demo-%02d@example.test', $index + 1)],
                [
                    'brand_id' => $brand->getKey(),
                    'motorcycle_id' => $motorcycle?->getKey(),
                    'location_id' => $locationId,
                    'customer_name' => $name,
                    'phone' => sprintf('+2010%08d', 1234500 + $index + 1),
                    'scheduled_at' => now()->startOfDay()->addDays($days)->setTime($hour, $minute),
                    'notes' => $notes,
                ],
            );
        }
    }
}
