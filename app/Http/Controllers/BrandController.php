<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BrandController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(['data' => Brand::withCount('products')->orderBy('id')->get()]);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(['data' => Brand::with('products')->findOrFail($id)]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate(['name' => 'required|string|max:100|unique:brands,name']);
        return response()->json(['data' => Brand::create($data)], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $brand = Brand::findOrFail($id);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('brands', 'name')->ignore($brand->id)],
        ]);
        $brand->update($data);
        return response()->json(['data' => $brand]);
    }

    public function destroy(int $id)
    {
        $brand = Brand::findOrFail($id);
        if ($brand->products()->exists()) {
            return response()->json(['message' => 'Merek masih digunakan produk'], 409);
        }
        $brand->delete();
        return response()->noContent();
    }
}
