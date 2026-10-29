<?php

namespace Database\Seeders;

use App\Models\FinancialPlan;
use Illuminate\Database\Seeder;

class FinancialPlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name_en' => 'Starter Plan',
                'name_ar' => 'الخطة الاقتصادية',
                'description_en' => 'Start with a 20% down payment and spread the remaining amount over 12 monthly installments.',
                'description_ar' => 'ادفع مقدم 20٪ وقسّط المبلغ المتبقي على 12 شهرًا.',
                'down_payment_percent' => 20,
                'installment_months' => 12,
                'sort_order' => 1,
            ],
            [
                'name_en' => 'Balanced Plan',
                'name_ar' => 'الخطة المتوازنة',
                'description_en' => 'Pay 30% upfront, then spread the remaining amount across 24 monthly installments.',
                'description_ar' => 'ادفع مقدم 30٪ وقسّط المبلغ المتبقي على 24 شهرًا.',
                'down_payment_percent' => 30,
                'installment_months' => 24,
                'sort_order' => 2,
            ],
            [
                'name_en' => 'Flexible Plan',
                'name_ar' => 'الخطة المرنة',
                'description_en' => 'Pay 40% upfront and spread the remaining amount across 36 monthly installments.',
                'description_ar' => 'ادفع مقدم 40٪ وقسّط المبلغ المتبقي على 36 شهرًا.',
                'down_payment_percent' => 40,
                'installment_months' => 36,
                'sort_order' => 3,
            ],
        ];

        foreach ($plans as $plan) {
            // Safe to rerun: don't override plans edited by admins.
            FinancialPlan::firstOrCreate(
                ['name_en' => $plan['name_en']],
                $plan + ['is_active' => true],
            );
        }
    }
}
