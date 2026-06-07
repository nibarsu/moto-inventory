<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBrandRequest;
use App\Http\Requests\UpdateBrandRequest;
use App\Models\Brand;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::orderBy('code')->paginate(10);

        return view('brands.index', compact('brands'));
    }

    public function create()
    {
        return view('brands.create');
    }

    public function store(StoreBrandRequest $request)
    {
        Brand::create($request->validated());

        return redirect()
            ->route('brands.index')
            ->with('success', '品牌新增成功');
    }

    public function show(Brand $brand)
    {
        return view('brands.show', compact('brand'));
    }

    public function edit(Brand $brand)
    {
        return view('brands.edit', compact('brand'));
    }

    public function update(UpdateBrandRequest $request, Brand $brand)
    {
        $brand->update($request->validated());

        return redirect()
            ->route('brands.index')
            ->with('success', '品牌更新成功');
    }

    public function destroy(Brand $brand)
    {
        $brand->delete();

        return redirect()
            ->route('brands.index')
            ->with('success', '品牌刪除成功');
    }
}