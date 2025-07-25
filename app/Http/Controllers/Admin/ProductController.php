<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::latest()->when(request()->q, function ($products) {
            $products = $products->where('title', 'like', '%' . request()->q . '%');
        })->paginate(10);

        return view('admin.product.index', compact('products'));

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::latest()->get();
        return view('admin.product.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'image'          => 'required|image|mimes:jpeg,jpg,png|max:2000',
            'title'          => 'required|unique:products',
            'category_id'    => 'required',
            'content'        => 'required',
            'weight'         => 'required',
            'price'          => 'required',
            'discount'       => 'required',
        ]);

        $image = $request->file('image');
        $image->storeAs('public/products', $image->hashName());

        $product = Product::create([
            'image'          => $image->hashName(),
            'title'          => $request->title,
            'slug'           => Str::slug($request->title, '-'),
            'category_id'    => $request->category_id,
            'content'        => $request->content,
            'weight'         => $request->weight,
            'price'          => $request->price,
            'discount'       => $request->discount,
            'keywords'       => $request->keywords,
            'description'    => $request->description
        ]);

        if ($product) {
            return redirect()->route('admin.product.index')->with(['success' => 'Data Berhasil Disimpan!']);
        } else {
            return redirect()->route('admin.product.index')->with(['error' => 'Data Gagal Disimpan!']);
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
    public function edit(Product $product)
    {
        $categories = Category::latest()->get();
        return view('admin.product.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'title'          => 'required|unique:products,title,' . $product->id,
            'category_id'    => 'required',
            'content'        => 'required',
            'weight'         => 'required',
            'price'          => 'required',
            'discount'       => 'required',
        ]);

        if ($request->file('image') == '') {

            $product = Product::findOrFail($product->id);
            $product->update([
                'title'          => $request->title,
                'slug'           => Str::slug($request->title, '-'),
                'category_id'    => $request->category_id,
                'content'        => $request->content,
                'weight'         => $request->weight,
                'price'          => $request->price,
                'discount'       => $request->discount,
                'keywords'       => $request->keywords,
                'description'    => $request->description
            ]);
        } else {

            Storage::disk('local')->delete('public/products/' . basename($product->image));

            $image = $request->file('image');
            $image->storeAs('public/products', $image->hashName());

            $product = Product::findOrFail($product->id);
            $product->update([
                'image'          => $image->hashName(),
                'title'          => $request->title,
                'slug'           => Str::slug($request->title, '-'),
                'category_id'    => $request->category_id,
                'content'        => $request->content,
                'weight'         => $request->weight,
                'price'          => $request->price,
                'discount'       => $request->discount,
                'keywords'       => $request->keywords,
                'description'    => $request->description
            ]);
        }

        if ($product) {
            return redirect()->route('admin.product.index')->with(['success' => 'Data Berhasil Diupdate!']);
        } else {
            return redirect()->route('admin.product.index')->with(['error' => 'Data Gagal Diupdate!']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $image = Storage::disk('local')->delete('public/products/' . basename($product->image));
        $product->delete();

        if ($product) {
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
