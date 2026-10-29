<?php

namespace App\Filament\Widgets;

use App\Enums\EnquiryStatus;
use App\Models\Brand;
use App\Models\Enquiry;
use App\Models\FinancialPlan;
use App\Models\Location;
use App\Models\Motorcycle;
use App\Models\Promotion;
use App\Models\Service;
use App\Models\TestRideBooking;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;

class SaqrOverviewStats extends StatsOverviewWidget
{
    protected int | string | array $columnSpan = 'full';

    // Counts do not need a database query every five seconds.
    protected ?string $pollingInterval = null;
    public static function canView(): bool
    {
        $user = auth()->user();

        return (bool) ($user?->is_active && ($user->isAdmin() || $user->isDataEntry()));
    }

    /**
     * Compute all visible counts with the same brand boundary as SAQR's resources.
     * In particular, an editor must never see totals for unassigned brands.
     *
     * @return array<string, int>
     */
    public static function countsFor(User $user): array
    {
        if (! $user->is_active || (! $user->isAdmin() && ! $user->isDataEntry())) {
            return [];
        }

        $isAdmin = $user->isAdmin();
        $brandIds = $isAdmin ? [] : $user->brands()->pluck('brands.id')->all();

        // Always apply restrictions in SQL, not after fetching all rows.
        $scope = static function (Builder $query) use ($isAdmin, $brandIds): Builder {
            return $isAdmin ? $query : $query->whereIn('brand_id', $brandIds);
        };

        $counts = [
            'brands' => $isAdmin
                ? Brand::query()->count()
                : count($brandIds),
            'motorcycles' => $scope(Motorcycle::query())->count(),
            'published_motorcycles' => $scope(Motorcycle::query())
                ->where('is_published', true)->count(),
            'new_enquiries' => $scope(Enquiry::query())
                ->where('status', EnquiryStatus::New->value)->count(),
            'test_rides_today' => $scope(TestRideBooking::query())
                ->whereDate('scheduled_at', today()->toDateString())->count(),
            'current_promotions' => $scope(Promotion::query())->current()->count(),
        ];

        if ($isAdmin) {
            $counts += [
                'active_users' => User::query()->where('is_active', true)->count(),
                'active_services' => Service::query()->where('is_active', true)->count(),
                'active_financial_plans' => FinancialPlan::query()->where('is_active', true)->count(),
                'locations' => Location::query()->count(),
            ];
        }

        return $counts;
    }

    protected function getStats(): array
    {
        $user = auth()->user();

        if (! $user || ! static::canView()) {
            return [];
        }

        $counts = static::countsFor($user);

        $stats = [
            Stat::make(__('saqr_dashboard.brands'), $counts['brands'])
                ->color('primary'),
            Stat::make(__('saqr_dashboard.motorcycles'), $counts['motorcycles']),
            Stat::make(__('saqr_dashboard.published_motorcycles'), $counts['published_motorcycles'])
                ->color('success'),
            Stat::make(__('saqr_dashboard.new_enquiries'), $counts['new_enquiries'])
                ->color('warning'),
            Stat::make(__('saqr_dashboard.test_rides_today'), $counts['test_rides_today'])
                ->color('info'),
            Stat::make(__('saqr_dashboard.current_promotions'), $counts['current_promotions'])
                ->color('success'),
        ];

        if ($user->isAdmin()) {
            $stats[] = Stat::make(__('saqr_dashboard.active_users'), $counts['active_users']);
            $stats[] = Stat::make(__('saqr_dashboard.active_services'), $counts['active_services']);
            $stats[] = Stat::make(__('saqr_dashboard.active_financial_plans'), $counts['active_financial_plans']);
            $stats[] = Stat::make(__('saqr_dashboard.locations'), $counts['locations']);
        }

        return $stats;
    }
}
