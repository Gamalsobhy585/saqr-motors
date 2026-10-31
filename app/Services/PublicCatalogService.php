<?php

namespace App\Services;

use App\Models\Brand;
use App\Models\FinancialPlan;
use App\Models\Location;
use App\Models\Motorcycle;
use App\Models\Promotion;
use App\Models\Service;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class PublicCatalogService
{
private function publishedMotorcycles(): Builder
{
    return Motorcycle::query()
        ->where('is_published', true)
        ->whereHas(
            'brand',
            fn (Builder $query) => $query->where('is_active', true)
        )
        ->with(['brand', 'primaryImage']);
}

    /** @return array<string, mixed> */
    public function home(): array
    {
        $brands = Brand::query()
            ->where('is_active', true)
            ->withCount(['motorcycles as published_motorcycles_count' => fn (Builder $query) =>
                $query->where('is_published', true)])
            ->orderBy('sort_order')->orderBy('id')->get();

$latest = $this->publishedMotorcycles()
    ->where('is_featured', true)
    ->latest('created_at')
    ->limit(9)
    ->get();
        $this->attachPromotions($latest);

        return [
            'brands' => $brands,
            'motorcycles' => $latest,
            'plans' => FinancialPlan::query()->active()->orderBy('sort_order')->limit(3)->get(),
            'services' => Service::query()->active()->orderBy('sort_order')->get(),
            'locations' => Location::query()->with('cityRelation')->orderBy('id')->limit(6)->get(),
        ];
    }

    /** @param array<string, mixed> $filters */
    public function index(array $filters): LengthAwarePaginator
    {
        $query = $this->publishedMotorcycles();
        if (filled($filters['brand'] ?? null)) {
            $query->whereHas('brand', fn (Builder $q) => $q->where('slug', $filters['brand']));
        }
        if (filled($filters['category'] ?? null)) {
            $query->where('category', (int) $filters['category']);
        }
        if (filled($filters['min_price'] ?? null)) {
            $query->where('starting_price', '>=', $filters['min_price']);
        }
        if (filled($filters['max_price'] ?? null)) {
            $query->where('starting_price', '<=', $filters['max_price']);
        }
        // Exact 'under' preset: strictly less than the selected threshold.
        if (filled($filters['price_under'] ?? null)) {
            $query->where('starting_price', '<', $filters['price_under']);
        }
        if ((string) ($filters['promotion'] ?? '') === '1' || filled($filters['promotion_id'] ?? null)) {
            $promotionId = $filters['promotion_id'] ?? null;
            $today = today()->toDateString();
            $query->whereExists(function ($sub) use ($today, $promotionId) {
                $sub->selectRaw('1')
                    ->from('promotions')
                    ->whereColumn('promotions.brand_id', 'motorcycles.brand_id')
                    ->when(filled($promotionId), fn ($q) => $q->where('promotions.id', $promotionId))
                    ->where('promotions.is_active', true)
                    ->whereDate('promotions.starts_at', '<=', $today)
                    ->whereDate('promotions.ends_at', '>=', $today)
                    ->where(fn ($q) => $q->whereNull('promotions.motorcycle_id')
                        ->orWhereColumn('promotions.motorcycle_id', 'motorcycles.id'));
            });
        }

        switch ($filters['sort'] ?? 'latest') {
            case 'price_asc':
                $query->orderByRaw('starting_price IS NULL ASC')
                    ->orderBy('starting_price')->orderBy('id');
                break;
            case 'price_desc':
                $query->orderByRaw('starting_price IS NULL ASC')
                    ->orderByDesc('starting_price')->orderBy('id');
                break;
            default:
                $query->latest('created_at')->latest('id');
        }

        $page = $query->paginate(12)->withQueryString();
        $this->attachPromotions($page->getCollection());
        return $page;
    }

    public function findPublished(string $brandSlug, string $motorcycleSlug): Motorcycle
    {
        $motorcycle = $this->publishedMotorcycles()
            ->with(['specification', 'images' => fn ($q) => $q->orderBy('sort_order')->orderBy('id')])
            ->where('slug', $motorcycleSlug)
            ->whereHas('brand', fn (Builder $query) => $query->where('slug', $brandSlug))
            ->firstOrFail();
        $this->attachPromotions(collect([$motorcycle]));
        return $motorcycle;
    }

    public function brands(): Collection
    {
        return Brand::query()->where('is_active', true)->orderBy('name')->get();
    }

    /** Attach the winning promotion without querying once per card. */
    public function attachPromotions(Collection $motorcycles): void
    {
        if ($motorcycles->isEmpty()) {
            return;
        }

        $promotions = Promotion::query()->current()
            ->whereIn('brand_id', $motorcycles->pluck('brand_id')->unique())
            ->orderByDesc('discount_percent')
            ->get();

        foreach ($motorcycles as $motorcycle) {
            // Specific offer always wins; otherwise highest brand-wide offer.
            $specific = $promotions->first(fn (Promotion $p) =>
                (int) $p->brand_id === (int) $motorcycle->brand_id && (int) $p->motorcycle_id === (int) $motorcycle->id);
            $general = $promotions->first(fn (Promotion $p) =>
                (int) $p->brand_id === (int) $motorcycle->brand_id && $p->motorcycle_id === null);
            $motorcycle->setRelation('activePromotion', $specific ?? $general);
        }
    }

    public static function imageUrl(Motorcycle $motorcycle): ?string
    {
        $path = $motorcycle->primaryImage?->path;

        if (blank($path)) {
            return null;
        }

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }

        if (str_starts_with($path, 'images/')) {
            return is_file(public_path($path))
                ? asset($path)
                : null;
        }

        $disk = Storage::disk('public');

        // Prioritize the actual uploaded image.
        if ($disk->exists($path)) {
            return $disk->url($path);
        }

        // Fallback to static assessment images.
        $filename = basename($path);

        if (in_array(
            $filename,
            [
                'cb650r.jpg',
                'cb650r-side.jpg',
                'mt-07.jpg',
                'mt-07-side.jpg',
                'ninja-650.jpg',
                'ninja-650-side.jpg',
                'v-strom-650.jpg',
                'v-strom-650-side.jpg',
                'r-1300-gs.jpg',
                'r-1300-gs-side.jpg',
                'monster.jpg',
                'monster-side.jpg',
                '390-adventure.webp',
                '390-adventure-side.webp',
                'tiger-900.jpg',
                'tiger-900-side.jpg',
            ],
            true
        )) {
            $staticPath = "images/motorcycles/{$filename}";

            if (is_file(public_path($staticPath))) {
                return asset($staticPath);
            }
        }

        return null;
    }
    public static function discountedPrice(Motorcycle $motorcycle): ?float
    {
        if ($motorcycle->starting_price === null) {
            return null;
        }
        $discount = (float) ($motorcycle->activePromotion?->discount_percent ?? 0);
        return round((float) $motorcycle->starting_price * (1 - $discount / 100), 2);
    }
}
