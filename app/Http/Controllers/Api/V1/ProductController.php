<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Resources\V1\ProductCollection;
use App\Http\Resources\V1\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\User;
use Intervention\Image\Facades\Image;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    // public function __construct() {
    //     $this->middleware(['role:admin', 'permission:manage product'])->except(['index']);
    // }
    
    public function index(){        
        $products = Product::with('category')->paginate(12);
        return response($products);

        $data = [];

        foreach ($products as $product) {
            $key = $product->id;
            if (!isset($data[$key])) {
                $data[$key] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price'=> $product->price,
                    'stock'=> $product->stock,
                    'storage'=> $product->storage,
                    'image'=> $product->image, 
                    'description'=> $product->description,
                    'isDisplayed' => $product->isDisplayed === 0 ? false : true,
                ];
            }
        }

        $itemData = \collect((\array_values($data)));
        
        // return response()->json($product);
        return new ProductCollection($products->setCollection($itemData));
        return new ProductCollection($itemData);
    }
    
    /**
     * Display the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function show($product){
        $product = Product::find($product);
        return new ProductResource($product);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){  
        // Log::info($request('image'));
        $test = request('image')->store('uploads','public');
        // dd($test);
        return response($test);      

        $data = $request->validated();

        $product = Product::create($data);
        
        return response()->json($product);

        
    }

    
    public function update(StoreProductRequest $request, Product $product){        
        $data = $request->validated();

        if($request->hasFile('image')){
            $imagePath = $data['image']->store('uploads','public');
            $data['image'] = $imagePath;
         }                

        $product->update($data);
        // return response()->json($product);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product){

        $product->delete();
        return response()->json("Deleted succesfully");
    }

    private function saveImage(UploadedFile $image)
    {
        $path = 'images/' . Str::random();
        if (!Storage::exists($path)) {
            Storage::makeDirectory($path, 0755, true);
        }
        if (!Storage::putFileAS('public/' . $path, $image, $image->getClientOriginalName())) {
            throw new \Exception("Unable to save file \"{$image->getClientOriginalName()}\"");
        }

        return $path . '/' . $image->getClientOriginalName();
    }

    public function search (Request $request)
    {
        $query = $request->input('name');
        $product = Product::where('name', 'LIKE', "%{$query}%")->get();
        return response()->json($product);
    }

    public function updateDisplayed($productId)
    {
        // Find the product by ID
        $product = Product::find($productId);

        // Check if the product exists
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        // Update the isAvailable field to false
        $product->update(['isDisplayed' => !$product->isDisplayed]);

        return response()->json(['message' => 'Product availability updated successfully']);
    }

    public function productChart(Request $request) //top 5 best seller
    {   //1 = weekly, 2 = monthly, 3 = annually
        if($request->timePeriod === 1){ //weekly
            
        }
        if($request->timePeriod === 2){ //monthly

        }
        if($request->timePeriod === 3){ //annually

        }

    }

}
