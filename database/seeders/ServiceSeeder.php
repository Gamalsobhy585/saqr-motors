<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class ServiceSeeder extends Seeder
{
    private const CREATOR_ID = 1;

    public function run(): void
    {
        $services = [
            [
                'slug' => 'periodic-maintenance',
                'name_en' => 'Periodic Maintenance',
                'name_ar' => 'الصيانة الدورية',
                'description_en' => 'Routine motorcycle maintenance, including fluid checks, chain care, and general safety inspections.',
                'description_ar' => 'خدمات الصيانة الدورية للدراجات النارية، وتشمل فحص السوائل والعناية بالسلسلة وفحوصات السلامة العامة.',
                'sort_order' => 1,
                'image' => 'periodic-maintenance.jpg',
            ],
            [
                'slug' => 'diagnostics-and-inspection',
                'name_en' => 'Diagnostics & Inspection',
                'name_ar' => 'الفحص وتشخيص الأعطال',
                'description_en' => 'Motorcycle inspections and fault diagnostics to help identify mechanical or electrical issues.',
                'description_ar' => 'فحص الدراجات النارية وتشخيص الأعطال للمساعدة في تحديد المشكلات الميكانيكية والكهربائية.',
                'sort_order' => 2,
                'image' => 'diagnostics-and-inspection.jpg',
            ],
            [
                'slug' => 'tires-and-batteries',
                'name_en' => 'Tires & Batteries',
                'name_ar' => 'الإطارات والبطاريات',
                'description_en' => 'Tire condition checks and battery inspection or replacement based on motorcycle specifications.',
                'description_ar' => 'فحص حالة الإطارات والبطاريات واستبدالها عند الحاجة وفقًا لمواصفات الدراجة النارية.',
                'sort_order' => 3,
                'image' => 'tires-and-batteries.jpg',
            ],
            [
                'slug' => 'parts-and-accessories',
                'name_en' => 'Parts & Accessories Installation',
                'name_ar' => 'تركيب قطع الغيار والإكسسوارات',
                'description_en' => 'Installation of compatible motorcycle replacement parts and accessories, subject to availability.',
                'description_ar' => 'تركيب قطع الغيار والإكسسوارات المتوافقة مع الدراجة النارية حسب التوافر.',
                'sort_order' => 4,
                'image' => 'parts-and-accessories.jpg',
            ],
        ];

        $disk = Storage::disk('public');

        foreach ($services as $service) {
            $filename = $service['image'];

            $source = public_path("images/services/{$filename}");

            $imagePath = null;

            if (is_file($source)) {
                $destination = "services/images/{$filename}";

                if (! $disk->exists($destination)) {
                    $saved = $disk->put(
                        $destination,
                        file_get_contents($source)
                    );

                    if (! $saved) {
                        throw new RuntimeException(
                            "Could not store service image: {$destination}"
                        );
                    }
                }

                $imagePath = $destination;
            } else {
                $this->command?->warn(
                    "Missing service image: {$source}"
                );
            }

            $record = Service::query()->firstOrCreate(
                ['slug' => $service['slug']],
                [
                    'name_en' => $service['name_en'],
                    'name_ar' => $service['name_ar'],
                    'description_en' => $service['description_en'],
                    'description_ar' => $service['description_ar'],
                    'sort_order' => $service['sort_order'],
                    'is_active' => true,
                    'image_path' => $imagePath,
                    'created_by' => self::CREATOR_ID,

                ]
            );
            if ($record->created_by === null) {
                $record->created_by = self::CREATOR_ID;
                $record->save();
            }


            // Fill the image only if it is currently missing.
            // Preserve images previously uploaded by admins.
            if (blank($record->image_path) && $imagePath !== null) {
                $record->image_path = $imagePath;
                $record->save();
            }
        }
    }
}
