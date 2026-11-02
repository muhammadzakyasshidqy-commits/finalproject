<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(['data' => Category::withCount('products')->orderBy('id')->get()]);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(['data' => Category::with('products')->findOrFail($id)]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate(['name' => 'required|string|max:100|unique:categories,name']);
        return response()->json(['data' => Category::create($data)], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $category = Category::findOrFail($id);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('categories', 'name')->ignore($category->id)],
        ]);
        $category->update($data);
        return response()->json(['data' => $category]);
    }

    public function destroy(int $id)
    {
        $category = Category::findOrFail($id);
        if ($category->products()->exists()) {
            return response()->json(['message' => 'Kategori masih digunakan produk'], 409);
        }
        $category->delete();
        return response()->noContent();
    }
}
