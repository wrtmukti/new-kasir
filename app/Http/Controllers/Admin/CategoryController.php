<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoryRequest;
use App\Models\Admin\Category;
use App\Models\SysAdmin\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::where('delete_status', 0)
            ->with('company')
            ->latest()
            ->paginate(10);
        return view('admin.category.index', compact('categories'));
    }

    public function data(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $categories = Category::where('delete_status', 0)
            ->with('company')
            ->latest()
            ->paginate($perPage);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.category._data', compact('categories'))->render(),
                'pagination' => $categories->links('vendor.pagination.modern')->toHtml(),
                'total' => $categories->total(),
                'from' => $categories->firstItem(),
                'to' => $categories->lastItem(),
            ]);
        }

        return view('admin.category.index', compact('categories'));
    }

    public function create()
    {
        $companies = Company::where('delete_status', 0)->where('company_status', 1)->get();
        return view('admin.category.create', compact('companies'));
    }

    public function store(CategoryRequest $request)
    {
        $validated = $request->validated();

        $validated['category_slug'] = str()->slug($validated['category_name']);
        $validated['category_status'] = $validated['category_status'] ?? 1;

        // Upload gambar
        if ($request->hasFile('category_image')) {
            $validated['category_image'] = $request->file('category_image')
                ->store('categories', 'public');
        }

        Category::create($validated);

        return redirect()->route('admin.category.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function show(Category $category)
    {
        $category->load('company');
        return view('admin.category.show', compact('category'));
    }

    public function edit(Category $category)
    {
        $companies = Company::where('delete_status', 0)->where('company_status', 1)->get();
        return view('admin.category.edit', compact('category', 'companies'));
    }

    public function update(CategoryRequest $request, Category $category)
    {
        $validated = $request->validated();

        $validated['category_slug'] = str()->slug($validated['category_name']);

        // Upload gambar baru
        if ($request->hasFile('category_image')) {
            // Hapus gambar lama
            if ($category->category_image) {
                Storage::disk('public')->delete($category->category_image);
            }
            $validated['category_image'] = $request->file('category_image')
                ->store('categories', 'public');
        } else {
            // Pertahankan gambar lama kalo gak upload baru
            unset($validated['category_image']);
        }

        $category->update($validated);

        return redirect()->route('admin.category.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category)
    {
        // Hapus file gambar
        if ($category->category_image) {
            Storage::disk('public')->delete($category->category_image);
        }

        $category->update(['delete_status' => 1]);

        if (request()->ajax()) {
            return response()->json(['success' => 'Kategori berhasil dihapus.']);
        }

        return redirect()->route('admin.category.index')
            ->with('success', 'Kategori berhasil dihapus.');
    }
}
