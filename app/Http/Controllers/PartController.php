<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePartRequest;
use App\Http\Requests\UpdatePartRequest;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Part;

class PartController extends Controller
{
    public function index()
    {
        $parts = Part::with(['brand', 'category'])
            ->orderBy('part_no')
            ->paginate(10);

        return view('parts.index', compact('parts'));
    }

    public function create()
    {
        return view('parts.create', [
            'brands' => $this->brands(),
            'categories' => $this->partCategories(),
        ]);
    }

    public function store(StorePartRequest $request)
    {
        Part::create($request->validated());

        return redirect()
            ->route('parts.index')
            ->with('success', '零件商品已建立。');
    }

    public function show(Part $part)
    {
        $part->load(['brand', 'category']);

        return view('parts.show', compact('part'));
    }

    public function edit(Part $part)
    {
        return view('parts.edit', [
            'part' => $part,
            'brands' => $this->brands(),
            'categories' => $this->partCategories(),
        ]);
    }

    public function update(UpdatePartRequest $request, Part $part)
    {
        $part->update($request->validated());

        return redirect()
            ->route('parts.index')
            ->with('success', '零件商品已更新。');
    }

    public function destroy(Part $part)
    {
        $part->delete();

        return redirect()
            ->route('parts.index')
            ->with('success', '零件商品已刪除。');
    }

    private function brands()
    {
        return Brand::where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    private function partCategories()
    {
        return Category::where('type', 'part')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }
}
