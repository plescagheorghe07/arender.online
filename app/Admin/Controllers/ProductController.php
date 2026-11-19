<?php

namespace App\Admin\Controllers;

use App\Admin\Requests\ProductRequest;
use App\Admin\Services\ProductAdminService;
use App\Catalog\Models\Product;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class ProductController extends Controller
{
    public function __construct(
        private readonly ProductAdminService $products,
    ) {}

    public function index(): View
    {
        return view('admin.products.index', [
            'products' => Product::query()->with('images')->orderBy('sort_order')->orderBy('id')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.products.form', [
            'product' => new Product(['is_active' => true, 'sort_order' => 0]),
            'priceRows' => old('prices', [['days' => '', 'price' => '', 'is_best' => false]]),
            'addonRows' => old('addons', []),
        ]);
    }

    public function store(ProductRequest $request): RedirectResponse
    {
        $product = $this->products->save(null, $request->validated(), $this->uploads($request), $request->validated('keep_images') ?? [], $this->addonImages($request));

        return redirect()
            ->route('admin.products.edit', $product)
            ->with('status', __('admin.saved'));
    }

    public function edit(Product $product): View
    {
        $product->load(['prices', 'addons', 'images']);

        return view('admin.products.form', [
            'product' => $product,
            'priceRows' => old('prices', $product->prices->map(fn ($price): array => [
                'days' => $price->days_label,
                'price' => $price->price,
                'is_best' => $price->is_best,
            ])->all()),
            'addonRows' => old('addons', $product->addons->map(fn ($addon): array => [
                'id' => $addon->id,
                'title_ro' => $addon->title_ro,
                'title_ru' => $addon->title_ru,
                'price' => $addon->price,
                'image' => $addon->image,
            ])->all()),
        ]);
    }

    public function update(ProductRequest $request, Product $product): RedirectResponse
    {
        $product = $this->products->save($product, $request->validated(), $this->uploads($request), $request->validated('keep_images') ?? [], $this->addonImages($request));

        return redirect()
            ->route('admin.products.edit', $product)
            ->with('status', __('admin.saved'));
    }

    public function reorder(Request $request): JsonResponse
    {
        $data = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer'],
        ]);

        $this->products->reorder($data['ids']);

        return response()->json(['ok' => true]);
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->products->delete($product);

        return redirect()->route('admin.products.index')->with('status', __('admin.deleted'));
    }

    /**
     * @return array<int, UploadedFile|null>
     */
    private function addonImages(ProductRequest $request): array
    {
        $images = [];
        foreach ($request->file('addons', []) as $index => $row) {
            $images[(int) $index] = $row['image'] ?? null;
        }

        return $images;
    }

    /**
     * @return array<int, UploadedFile|null>
     */
    private function uploads(ProductRequest $request): array
    {
        $files = $request->file('images', []);

        return is_array($files) ? $files : [];
    }
}
