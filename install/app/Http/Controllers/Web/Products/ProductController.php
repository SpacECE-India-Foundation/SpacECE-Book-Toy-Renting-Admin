<?php

namespace App\Http\Controllers\Web\Products;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\AppSetting;
use App\Models\Product;
use App\Repositories\ProductRepository;
use Illuminate\Http\Request;
use App\Repositories\StoreRepository;
use App\Models\Service; 

class ProductController extends Controller
{
    private $productRepo;
    private $shopRepo;
    public function __construct(ProductRepository $productRepository, StoreRepository $shopRepository)
    {
        $this->productRepo = $productRepository;
        $this->shopRepo= $shopRepository;
    }

    public function index()
    {
        $currency = AppSetting::first()?->currency ?? '$';
        $products = $this->productRepo->getAllOrFindBySearch(true);

        return view('products.index', compact('products', 'currency'));
    }

    public function create()
    {
        $currency = AppSetting::first()?->currency ?? '$';
        if (auth()->user()->hasAnyRole(['admin', 'root', 'store'])) {
            $services = \App\Models\Service::all();
        } else {
            $services = auth()->user()->store ? auth()->user()->store->services : collect(); 
        }
        if (auth()->user()->hasAnyRole(['admin', 'root'])) {
            // Admin and root can access all shops
            $shops = \App\Models\Store::all(['id', 'name']);
        } elseif (auth()->user()->hasRole('store')) {
            // Store managers can only access their own shop
            $shops = auth()->user()->store ? collect([auth()->user()->store]) : collect();
        } else {
            // Other roles get no shops
            $shops = collect();
        }
        

        return view('products.create', compact('services', 'currency', 'shops'));
    }

    public function store(ProductRequest $request)
    {
        if (($request->discount_price != '') && ($request->price < $request->discount_price)) {
            return back()->with('error', 'Discount price must be less than product price');
        }
        $this->productRepo->storeByRequest($request);

        return redirect()->route('product.index')->with('success', 'Product added successsfully');
    }

    public function edit(Product $product)
    {
        $variants = $product->service->variants();
    
        // Grant full access to admin and root
        if (auth()->user()->hasAnyRole(['admin', 'root'])) {
            $variants = $variants->get(); // Fetch all variants without restriction
            $services = Service::all();  // Fetch all services globally
        } 
        // Keep restrictions for the store role
        elseif (auth()->user()->hasRole('store')) {
            $variants = $variants->where('store_id', auth()->user()->store->id)->get();
            $services = auth()->user()->store->services;
        } 
        // Handle cases where the user doesn't belong to the above roles
        else {
            abort(403, 'Unauthorized action.');
        }
    
        return view('products.edit', compact('product', 'services', 'variants'));
    }
    

    public function update(ProductRequest $request, Product $product)
    {
        if (($request->discount_price != '') && ($request->price < $request->discount_price)) {
            return back()->with('error', 'Product price must be bigger than discount price');
        }
        $this->productRepo->updateByRequest($request, $product);

        return redirect()->route('product.index')->with('success', 'Product updated success');
    }

    public function toggleActivationStatus(Product $product)
    {
        $this->productRepo->updateStatusById($product);

        return back()->with('success', 'product status updated');
    }

    public function orderUpdate(Request $request, Product $product)
    {

        $product->update([
            'order' => $request->position ?? 0,
        ]);

        return back();
    }
}
