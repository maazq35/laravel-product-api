<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductImage;
use Validator;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller {
    // index: GET /api/products
    public function index(){
        $products = Product::with('images')->get()->map(function($p){
            return [
                'id'=>$p->id,
                'name'=>$p->name,
                'price'=>$p->price,
                'description'=>$p->description,
                'images'=> $p->images->map(function($img){ return ['id'=>$img->id,'url'=>$img->url,'alt'=>$img->alt_text]; })
            ];
        });
        return response()->json($products);
    }

    public function show($id){
        $p = Product::with('images')->findOrFail($id);
        return response()->json($p);
    }

    // store product with multiple images
    public function store(Request $request)
    {
        $v = Validator::make($request->all(), [
            'name'        => 'required|string|max:255',
            'price'       => 'required|numeric',
            'description' => 'nullable|string',
            'images.*'    => 'nullable|image|max:5120' // 5MB each
        ]);

        if ($v->fails()) {
            return response()->json($v->errors(), 422);
        }

        // Create the product
        $product = Product::create($request->only('name', 'price', 'description'));
       

        // Handle image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('products', 'public');
                $product->images()->create([
                    'product_id' => $product->id,
                    'path'       => $path
                ]);
            }
        }   else {
        \Log::info('No images detected in request');
    }

        // Return product with full image URLs
        $product->load(['images' => function ($q) {
            $q->select('id', 'product_id', 'path')
              ->addSelect(\DB::raw("CONCAT('" . asset('storage') . "/', path) as url"));
        }]);

        return response()->json($product, 201);
    }

    public function update(Request $request, $id){
        $product = Product::findOrFail($id);
        $product->update($request->only('name','price','description'));
        // Optionally add images
        if($request->hasFile('images')){
            foreach($request->file('images') as $file){
                $path = $file->store('products','public');
                $product->images()->create(['path'=>$path]);
            }
        }
        return response()->json($product->load('images'));
    }

    public function destroy($id){
        $product = Product::findOrFail($id);
        // Delete image files
        foreach($product->images as $img){
            Storage::disk('public')->delete($img->path);
        }
        $product->delete();
        return response()->json(['message'=>'Deleted']);
    }

    // optional: remove a single image
    public function deleteImage($id){
        $img = ProductImage::findOrFail($id);
        Storage::disk('public')->delete($img->path);
        $img->delete();
        return response()->json(['message'=>'Image deleted']);
    }
}
