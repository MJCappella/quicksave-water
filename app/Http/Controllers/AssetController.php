<?php

namespace App\Http\Controllers;

use App\Models\FixedAsset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AssetController extends Controller
{
    public function index(Request $request): View
    {
        $selectedCategory = $request->query('category');
        $query = FixedAsset::query();

        if ($selectedCategory) {
            $query->where('category', $selectedCategory);
        }

        $assets = $query->latest()->get();
        $categories = FixedAsset::pluck('category')->unique()->values();

        $totalOriginalCost = $assets->sum('purchase_cost');
        $totalAccumulatedDepr = $assets->sum('accumulated_depreciation');
        $totalNetBookValue = $assets->sum('current_book_value');
        $activeAssetsCount = $assets->where('status', 'Operational')->count();

        return view('assets.index', compact(
            'assets',
            'categories',
            'selectedCategory',
            'totalOriginalCost',
            'totalAccumulatedDepr',
            'totalNetBookValue',
            'activeAssetsCount'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'asset_code' => 'required|string|unique:fixed_assets,asset_code',
            'name' => 'required|string|max:255',
            'category' => 'required|string',
            'serial_no' => 'nullable|string|max:100',
            'purchase_date' => 'required|date',
            'purchase_cost' => 'required|numeric|min:0',
            'salvage_value' => 'nullable|numeric|min:0',
            'useful_life_years' => 'required|integer|min:1',
            'depreciation_method' => 'required|string|in:Straight-Line,Reducing Balance',
            'annual_depreciation_rate' => 'required|numeric|min:0|max:100',
            'location' => 'required|string|max:255',
            'assigned_to' => 'nullable|string|max:255',
            'status' => 'required|string',
            'next_maintenance_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $cost = (float) $validated['purchase_cost'];
        $salvage = (float) ($validated['salvage_value'] ?? 0);
        $life = (int) $validated['useful_life_years'];
        $yearsElapsed = max(0, now()->diffInDays($validated['purchase_date']) / 365.25);

        $accum = 0;
        if ($validated['depreciation_method'] === 'Straight-Line') {
            $annual = $life > 0 ? ($cost - $salvage) / $life : 0;
            $accum = min($cost - $salvage, $annual * $yearsElapsed);
        } else {
            $rate = ((float) $validated['annual_depreciation_rate']) / 100;
            $cur = $cost;
            for ($i = 0; $i < floor($yearsElapsed); $i++) {
                $cur *= (1 - $rate);
            }
            $accum = max(0, $cost - $cur);
        }

        $bookValue = max($salvage, $cost - $accum);

        $validated['accumulated_depreciation'] = round($accum, 2);
        $validated['current_book_value'] = round($bookValue, 2);

        FixedAsset::create($validated);

        return redirect()->route('assets.index')->with('success', 'Fixed asset registered successfully with computed depreciation schedule!');
    }
}
