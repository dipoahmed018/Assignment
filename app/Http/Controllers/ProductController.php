<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductCreate;
use App\Http\Requests\ProductUpdate;
use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{

    public function index()
    {
        $products = DB::table('products')->orderBy('id', 'desc')->cursorPaginate(10,'id');
        return response()->json(['products' => $products]);
    }
    public function getProduct(Products $product)
    {
       $product->load('owner_details');
       return response()->json(['product' => $product]);
    }
    public function createProduct(ProductCreate $request)
    {
        $image = $request->file('image');
        $image_name = Str::uniqid() .'.'. $image->extension();
        $image->storePubliclyAs('/products/images', $image_name);

        $product = Products::create([
            'title' => $request->title,
            'description' => $request->description,
            'price' => $request->price,
            'owner' => $request->user()->id,
            'image' => asset('/storage/products/images/'. $image_name),
        ]);
        return response()->json(['product' => $product], 201);
    }

    public function updateProduct(ProductUpdate $request, Products $product)
    {
        $product->title = $request->title ?? $product->title;
        $product->description == $request->description ?? $product->description;
        $product->price == $request->price ?? $product->price;

        //image update
        $image = $request->file('image');
        if ($image) {
            $image_name = Str::uniqid() .'.'. $image->extension();
            
            //delte old file
            $location = strstr($product->image,'products');
            Storage::disk('public')->delete($location);
            
            //save new file
            $image->storePubliclyAs('/products/images', $image_name);
            $product->image = asset('/storage/products/images/'. $image_name);
        }
        $product->save();
        return response()->json(['product' => $product]);
    }

    public function deleteProduct(Request $request, Products $product)
    {
        if ($request->user()->cannot('update', $product)) {
            response()->json(['message' => "Your doesn't have permission to delete this product"], 403);
        }
        $product->delete();
        return response()->json(['message' => 'product deleted']);
    }
}
