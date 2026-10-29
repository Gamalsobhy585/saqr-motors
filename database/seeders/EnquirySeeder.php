<?php

namespace Database\Seeders;

use App\Enums\EnquiryStatus;
use App\Enums\EnquiryType;
use App\Models\Brand;
use App\Models\Enquiry;
use Illuminate\Database\Seeder;

class EnquirySeeder extends Seeder
{
    private const UPDATER_ID = 1;

    public function run(): void
    {
        // Seed BrandSeeder first. Motorcycle is optional, so brand-only enquiries
        // still work when MotorcycleSeeder has not been run yet.
        $entries = [
            ['honda', 'CB650R', 'أحمد محمد عبدالعزيز', 'ahmed.abdelaziz@example.com', '01012345671', EnquiryType::TestRide, EnquiryStatus::New, 'السلام عليكم، هل يمكن حجز تجربة قيادة لهوندا CB650R يوم الجمعة؟'],
            ['yamaha', 'MT-07', 'سارة محمود علي', 'sara.mahmoud@example.com', '01012345672', EnquiryType::Financing, EnquiryStatus::Contacted, 'أريد معرفة نظام التقسيط والمقدم المطلوب لدراجة ياماها MT-07.'],
            ['kawasaki', 'Ninja 650', 'محمد إبراهيم حسن', 'mohamed.ibrahim@example.com', '01012345673', EnquiryType::TestRide, EnquiryStatus::New, 'هل يوجد موعد لتجربة قيادة كاواساكي نينجا 650؟'],
            ['suzuki', 'V-Strom', 'نورهان أحمد فتحي', 'nourhan.fathy@example.com', '01012345674', EnquiryType::General, EnquiryStatus::Closed, 'هل لديكم فروع في الإسكندرية؟ وما مواعيد العمل؟'],
            ['bmw-motorrad', 'R 1300 GS', 'خالد عبد الرحمن', 'khaled.abdelrahman@example.com', '01012345675', EnquiryType::Financing, EnquiryStatus::New, 'ما أسعار بي إم دبليو R 1300 GS وخيارات التمويل المتاحة؟'],
            ['ducati', 'Monster', 'مريم حسن الشاذلي', 'mariam.shazly@example.com', '01012345676', EnquiryType::General, EnquiryStatus::Contacted, 'أرغب في معرفة الألوان المتاحة وموعد التسليم لدراجة دوكاتي مونستر.'],
            ['ktm', '390 Adventure', 'يوسف أحمد صبحي', 'youssef.sobhy@example.com', '01012345677', EnquiryType::TestRide, EnquiryStatus::New, 'هل تجربة قيادة KTM 390 Adventure متاحة هذا الأسبوع؟'],
            ['triumph', 'Tiger 900', 'دينا مصطفى سالم', 'dina.salem@example.com', '01012345678', EnquiryType::General, EnquiryStatus::New, 'هل توجد ضمانات وصيانة دورية متوفرة لترايمف تايجر 900؟'],
            ['honda', null, 'عبدالله السيد محمود', 'abdallah.elsayed@example.com', '01012345679', EnquiryType::General, EnquiryStatus::New, 'أبحث عن موتوسيكل مناسب للاستخدام اليومي داخل القاهرة، ماذا تنصحون؟'],
            ['yamaha', null, 'فاطمة محمد عادل', 'fatma.adel@example.com', '01012345680', EnquiryType::Financing, EnquiryStatus::Closed, 'هل يوجد تقسيط بدون فوائد؟ وما المستندات المطلوبة؟'],
            ['kawasaki', null, 'مصطفى حسين إبراهيم', 'mostafa.hussein@example.com', '01012345681', EnquiryType::General, EnquiryStatus::Contacted, 'أريد معرفة قائمة الأسعار والعروض الحالية.'],
            ['honda', null, 'إيمان عبد الحميد', 'eman.abdelhamid@example.com', '01012345682', EnquiryType::Financing, EnquiryStatus::New, 'أرغب في شراء دراجة بالتقسيط، هل يمكن التواصل معي للتفاصيل؟'],
            ['suzuki', null, 'عمر طارق يوسف', 'omar.tarek@example.com', '01012345683', EnquiryType::TestRide, EnquiryStatus::New, 'هل يمكن ترتيب تجربة قيادة في نهاية الأسبوع؟'],
            ['ducati', null, 'بسمة أشرف جلال', 'basma.ashraf@example.com', '01012345684', EnquiryType::General, EnquiryStatus::Closed, 'هل لديكم إكسسوارات أصلية وخدمة تركيب؟'],
        ];

        foreach ($entries as [$slug, $bikeName, $name, $email, $phone, $type, $status, $message]) {
            $brand = Brand::query()->where('slug', $slug)->first();

            if (! $brand) {
                continue;
            }

            $motorcycle = $bikeName
                ? $brand->motorcycles()->where('name', 'like', '%' . $bikeName . '%')->first()
                : null;

            $isHandled = in_array(
                $status,
                [EnquiryStatus::Contacted, EnquiryStatus::Closed],
                true
            );

            $enquiry = Enquiry::query()->firstOrCreate(
                ['email' => $email],
                [
                    'brand_id' => $brand->id,
                    'motorcycle_id' => $motorcycle?->id,
                    'name' => $name,
                    'phone' => $phone,
                    'type' => $type,
                    'status' => $status,
                    'message' => $message,
                    'updated_by' => $isHandled ? self::UPDATER_ID : null,
                ],
            );

            // Backfill rows that already existed before this change.
            if ($isHandled && $enquiry->updated_by === null) {
                $enquiry->updated_by = self::UPDATER_ID;
                $enquiry->save();
            }
        }
    }
}