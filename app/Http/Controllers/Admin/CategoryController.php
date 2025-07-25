<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::latest()->when(request()->q, function ($categories) {
            $categories = $categories->where('name', 'like', '%' . request()->q . '%');
        })->paginate(10);

        return view('admin.category.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.category.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,jpg,png|max:2000',
            'name'  => 'required|unique:categories'
        ]);

        $image = $request->file('image');
        $image->storeAs('public/categories', $image->hashName());

        $category = Category::create([
            'image'  => $image->hashName(),
            'name'   => $request->name,
            'slug'   => Str::slug($request->name, '-')
        ]);

        if ($category) {
            return redirect()->route('admin.category.index')->with(['success' => 'Data Berhasil Disimpan!']);
        } else {
            return redirect()->route('admin.category.index')->with(['error' => 'Data Gagal Disimpan!']);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        return view('admin.category.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name'  => 'required|unique:categories,name,' . $category->id
        ]);

        if ($request->file('image') == '') {

            $category = Category::findOrFail($category->id);
            $category->update([
                'name'   => $request->name,
                'slug'   => Str::slug($request->name, '-')
            ]);
        } else {

            Storage::disk('local')->delete('public/categories/' . basename($category->image));

            $image = $request->file('image');
            $image->storeAs('public/categories', $image->hashName());

            $category = Category::findOrFail($category->id);
            $category->update([
                'image'  => $image->hashName(),
                'name'   => $request->name,
                'slug'   => Str::slug($request->name, '-')
            ]);
        }

        if ($category) {
            return redirect()->route('admin.category.index')->with(['success' => 'Data Berhasil Diupdate!']);
        } else {
            return redirect()->route('admin.category.index')->with(['error' => 'Data Gagal Diupdate!']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $image = Storage::disk('local')->delete('public/categories/' . basename($category->image));
        $category->delete();

        if ($category) {
            return response()->json([
                'status' => 'success'
            ]);
        } else {
            return response()->json([
                'status' => 'error'
            ]);
        }
    }
}
