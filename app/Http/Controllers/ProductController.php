<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $filters = $request->validate([
            'keyword' => 'sometimes|nullable|string|max:100',
            'category_id' => 'sometimes|integer|exists:categories,id',
            'brand_id' => 'sometimes|integer|exists:brands,id',
            'sort' => 'sometimes|in:name_asc,name_desc,price_asc,price_desc,newest,oldest',
            'per_page' => 'sometimes|integer|between:1,100',
            'page' => 'sometimes|integer|min:1',
        ]);

        $query = Product::query()->with(['category', 'brand']);

        if (isset($filters['keyword']) && trim($filters['keyword']) !== '') {
            $query->where('name', 'like', '%' . trim($filters['keyword']) . '%');
        }
        if (isset($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }
        if (isset($filters['brand_id'])) {
            $query->where('brand_id', $filters['brand_id']);
        }

        switch ($filters['sort'] ?? 'newest') {
            case 'name_asc': $query->orderBy('name')->orderBy('id'); break;
            case 'name_desc': $query->orderByDesc('name')->orderByDesc('id'); break;
            case 'price_asc': $query->orderBy('price')->orderBy('id'); break;
            case 'price_desc': $query->orderByDesc('price')->orderByDesc('id'); break;
            case 'oldest': $query->orderBy('id'); break;
            default: $query->orderByDesc('id');
        }

        return response()->json(
            $query->paginate($filters['per_page'] ?? 10)->appends($request->query())
        );
    }

    public function show(int $id): JsonResponse
    {
        $product = Product::with(['category', 'brand'])->findOrFail($id);
        return response()->json(['data' => $product]);
    }

    private function rules(bool $partial = false): array
    {
        $rules = [
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:5000',
            'image_url' => 'nullable|string|url:http,https|max:2048',
            'price' => 'required|numeric|min:0|max:99999999.99',
            'stock' => 'required|integer|min:0|max:2147483647',
            'category_id' => 'required|integer|exists:categories,id',
            'brand_id' => 'required|integer|exists:brands,id',
        ];
        if ($partial) {
            foreach ($rules as $key => $rule) {
                $rules[$key] = 'sometimes|' . $rule;
            }
        }
        return $rules;
    }

    public function store(Request $request): JsonResponse
    {
        $product = Product::create($request->validate($this->rules()));
        return response()->json(['data' => $product->load(['category', 'brand'])], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $product = Product::findOrFail($id);
        $data = $request->validate($this->rules($request->isMethod('patch')));
        $product->update($data);
        return response()->json(['data' => $product->load(['category', 'brand'])]);
    }

    public function destroy(int $id)
    {
        Product::findOrFail($id)->delete();
        return response()->noContent();
    }
}
