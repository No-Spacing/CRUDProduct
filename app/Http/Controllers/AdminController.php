<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

use App\Models\Product;
use App\Models\category;


use Inertia\Inertia;

class AdminController extends Controller
{
    
    public function PostLogin (Request $request) {

        $request->validate([
            'username' => ['required'],
            'password' => ['required']
        ]);

        if (Auth::guard('admin')->attempt(['username' => $request->username, 'password' => $request->password], $request->remember) ) {
            $request->session()->regenerate();
            return redirect()->intended('dashboard');
        } else {
            return back()->with('message','Invalid credentials');
        }

    }

    public function RegisterUser (Request $request){

        $request->validate([
            'username' => [ 'required', 'unique:admins', 'min:6' ],
            'password' => [ 'required', 'confirmed', 'min:6' ]
        ]);

        DB::table('admins')
        ->insert([
            'username' => $request->username,
            'password' => hash::make($request->password),
        ]);

        return redirect('')->with('snackbar', true);
    }

    public function Products (Request $request) {

        $products = Product::query()
                    ->when($request->filter, function($query, $filter){
                        $query->where('categories.name', '=', $filter);
                    }) 
                    ->when($request->search, function($query, $search){
                        $query->where('products.name', 'like', '%' . $search . '%')
                        ->orWhere('products.description', 'like', '%' . $search . '%');
                    });
                        
        return Inertia::render('Products', [
            'products' => $products->select('products.*', 'categories.name as category')
                                    ->join('categories', 'products.id', '=', 'categories.product_id')
                                    ->paginate(5)
                                    ->withQueryString(),
            'category' => Category::all()
        ]);
    }

    public function PostProduct (Request $request) {

        if($request->productDetails == 1){
            $request->validate(
                [
                    'name' => 'required',
                    'category' => 'required',
                    'description' => 'required'
                ]
            );
        } else if ($request->productDetails == 2){
            $request->validate(
                [
                    'image' => [ 'required', 'array' ],            
                    'image.*' => [ 'image' ]
                ]
            );
        } else if ($request->productDetails == 3){
            $request->validate(
                [
                    'date_time' => 'required'
                ]
            );
            
            $products = new Product();

            $products->name = $request->name;
            $products->description = $request->description;
            $products->date_time = $request->date_time;
            $products->save();

            $categories = new category();

            $categories->product_id = $products->id;
            $categories->name = $request->category;
            $categories->save();

            return redirect('/products');
        }
        
    }

    public function UpdateProduct (Request $request) {

        $request->validate([
            'name' => 'required',
            'category' => 'required',
            'description' => 'required',
            'date_time' => 'required'
        ]);

        DB::table('products')
        ->where('id', $request->id)
        ->update([
            'name' => $request->name,
            'category' => $request->category,
            'description' => $request->description,
            'date_time' => $request->date_time,
        ]);

    }

    public function DeleteProduct ($id) {
        DB::table('products')->where('id', $id)->delete();
    }

    public function VideoPlayer($id){
        $videoFile = DB::table('video_tables')->where('id', $id)->first();
        return Inertia::render('Videos', ['videoFile' => $videoFile]);
    }


    public function logout (Request $request) {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login'); 
    }
}
