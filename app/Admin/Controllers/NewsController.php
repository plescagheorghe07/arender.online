<?php

namespace App\Admin\Controllers;

use App\Admin\Requests\NewsRequest;
use App\Admin\Services\NewsAdminService;
use App\Http\Controllers\Controller;
use App\News\Models\NewsArticle;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class NewsController extends Controller
{
    public function __construct(
        private readonly NewsAdminService $articles,
    ) {}

    public function index(): View
    {
        return view('admin.news.index', [
            'articles' => NewsArticle::query()->with('images')->orderBy('sort_order')->orderByDesc('published_on')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.news.form', [
            'article' => new NewsArticle([
                'is_published' => true,
                'published_on' => now()->toDateString(),
            ]),
        ]);
    }

    public function store(NewsRequest $request): RedirectResponse
    {
        $article = $this->articles->save(null, $request->validated(), $this->uploads($request), $request->validated('keep_images') ?? []);

        return redirect()->route('admin.news.edit', $article)->with('status', __('admin.saved'));
    }

    public function edit(NewsArticle $article): View
    {
        $article->load('images');

        return view('admin.news.form', [
            'article' => $article,
        ]);
    }

    public function update(NewsRequest $request, NewsArticle $article): RedirectResponse
    {
        $article = $this->articles->save($article, $request->validated(), $this->uploads($request), $request->validated('keep_images') ?? []);

        return redirect()->route('admin.news.edit', $article)->with('status', __('admin.saved'));
    }

    public function reorder(Request $request): JsonResponse
    {
        $data = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer'],
        ]);

        $this->articles->reorder($data['ids']);

        return response()->json(['ok' => true]);
    }

    public function destroy(NewsArticle $article): RedirectResponse
    {
        $this->articles->delete($article);

        return redirect()->route('admin.news.index')->with('status', __('admin.deleted'));
    }

    /**
     * @return array<int, UploadedFile|null>
     */
    private function uploads(NewsRequest $request): array
    {
        $files = $request->file('images', []);

        return is_array($files) ? $files : [];
    }
}
