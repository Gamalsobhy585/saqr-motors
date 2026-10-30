<?php

namespace App\Http\Controllers\Public;

use App\Enums\MotorcycleCategory;
use App\Http\Controllers\Controller;
use App\Services\PublicCatalogService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CatalogController extends Controller
{
    public function home(PublicCatalogService $catalog)
    {
        return view('public.home', $catalog->home());
    }

    public function index(Request $request, PublicCatalogService $catalog)
    {
        $filters = $request->validate([
            'brand' => ['nullable', 'string', 'max:160'],
            'category' => ['nullable', Rule::in(array_map(fn ($c) => $c->value, MotorcycleCategory::cases()))],
            'min_price' => ['nullable', 'numeric', 'min:0'],
            'max_price' => ['nullable', 'numeric', 'min:0', 'gte:min_price'],
            'price_under' => ['nullable', 'integer', Rule::in([100000, 150000, 200000])],
            'promotion' => ['nullable', Rule::in(['1'])],
            'promotion_id' => ['nullable', 'integer', 'min:1'],
            'sort' => ['nullable', Rule::in(['latest', 'price_asc', 'price_desc'])],
        ]);

        return view('public.motorcycles.index', [
            'motorcycles' => $catalog->index($filters),
            'brands' => $catalog->brands(),
            'promotions' => \App\Models\Promotion::query()->current()
                ->whereHas('brand', fn ($q) => $q->where('is_active', true))
                ->orderBy('sort_order')->limit(30)->get(),
            'filters' => $filters,
        ]);
    }

    public function show(string $brand, string $motorcycle, PublicCatalogService $catalog)
    {
        return view('public.motorcycles.show', [
            'motorcycle' => $catalog->findPublished($brand, $motorcycle),
            'locations' => \App\Models\Location::query()
                ->with('cityRelation')->orderBy('name_en')->get(),
            'rideSlots' => config('saqr_public.ride_slots'),
        ]);
    }
}
