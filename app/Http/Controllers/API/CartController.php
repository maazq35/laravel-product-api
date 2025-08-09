<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Product;
use Validator;

class CartController extends Controller {
    // Add to cart (user id hardcoded to 1 as requested)
    public function addToCart(Request $request){
        $v = Validator::make($request->all(), [
            'product_id'=>'required|exists:products,id',
            'quantity'=>'nullable|integer|min:1'
        ]);
        if($v->fails()) return response()->json($v->errors(), 422);

        $userId = 1; // hardcoded per PHASE 2 instruction
        $productId = $request->product_id;
        $qty = $request->quantity ?? 1;

        // If entry exists, increase quantity
        $cart = Cart::where('user_id',$userId)->where('product_id',$productId)->first();
        if($cart){
            $cart->quantity += $qty;
            $cart->save();
        } else {
            $cart = Cart::create(['user_id'=>$userId,'product_id'=>$productId,'quantity'=>$qty]);
        }
        return response()->json($cart->load('product.images'), 201);
    }

    // Backend GET: display all products in cart for user id (hardcoded user 1)
    public function listForBackend(Request $request){
        $userId = 1; // per spec
        $items = Cart::with('product.images')->where('user_id',$userId)->get()->map(function($c){
            return [
                'cart_id'=>$c->id,
                'product_id'=>$c->product->id,
                'product_name'=>$c->product->name,
                'price'=>$c->product->price,
                'quantity'=>$c->quantity,
                'images'=>$c->product->images->map(fn($i)=>['id'=>$i->id,'url'=>$i->url]),
                'added_at'=>$c->created_at,
            ];
        });
        return response()->json($items);
    }

    // Optionally: remove from cart
    public function remove(Request $request, $id){
        $cart = Cart::findOrFail($id);
        $cart->delete();
        return response()->json(['message'=>'Removed']);
    }
}
