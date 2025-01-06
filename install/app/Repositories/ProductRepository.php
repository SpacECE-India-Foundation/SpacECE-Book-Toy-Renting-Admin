<?php

namespace App\Repositories;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;
// use QrCode;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Log;

class ProductRepository extends Repository
{
    private $path = 'public/images/products/';

    public function model()
    {
        return Product::class;
    }

    public function getAllOrFindBySearch($isLatest = false)
    {
        $user = auth()->user();
        $products = $this->model()::query();
        if ($user->hasRole('store')) {
            $storId = $user->store->id;
            $products = $products->where('store_id', $storId);
        }
        $searchKey = \request('search');

        if ($searchKey) {
            $products = $products->where('name', 'like', "%{$searchKey}%")
                ->orWhere('price', 'like', "%{$searchKey}%")
                ->orWhere('discount_price', 'like', "%{$searchKey}%");
        }

        if ($isLatest) {
            $products->latest('id');
        }

        return $products->get();
    }

    public function getByRequest($request)
    {
        $variantId = $request->variant_id;
        $serviceId = $request->service_id;
        $searchKey = $request->search;
        $storeId = $request->store_id;

        $products = $this->model()::query()
            ->when($serviceId, function ($query) use ($serviceId) {
                $query->where('service_id', $serviceId);
            })
            ->when($variantId, function ($query) use ($variantId) {
                $query->where('variant_id', $variantId);
            })
            ->when($storeId, function ($query) use ($storeId) {
                $query->where('store_id', $storeId);
            })->when($searchKey, function ($query) use ($searchKey) {
                $query->where('name', 'like', "%{$searchKey}%")
                    ->orWhere('price', 'like', "%{$searchKey}%");
            });

        return $products->orderBy('order', 'asc')->isActive()->get();
    }

    public function storeByRequest(ProductRequest $request): Product
{
    // Store the thumbnail using the MediaRepository
    $thumbnail = (new MediaRepository())->storeByRequest(
        $request->image,
        $this->path,
        'This image is for the product thumbnail',
        'image'
    );

    // Generate QR code URL if necessary (implementation not provided)
    $qrcodeURl = ''; // You can add logic here if required to generate the QR code URL
    $user = auth()->user();

    // Check if the user has 'admin' or 'root' role
    if ($user->hasRole('admin') || $user->hasRole('root')) {
        $storeId= $request->shop_id;
    
    } else {
        $storeId=auth()->user()->store?->id; 
    }
    if (!$storeId) {
        throw new \Exception('Store ID is required for this product.'); // Optional error handling
    }

    // Create the product
    return $this->create([
        'name' => $request->name,
        'name_bn' => $request->name_bn,
        'slug' => $request->slug,
        'thumbnail_id' => $thumbnail->id,
        'store_id' => $storeId,
        'service_id' => $request->service_id,
        'variant_id' => $request->variant_id,
        'discount_price' => $request->discount_price,
        'price' => $request->price,
        'qrcode_url' => $qrcodeURl,
        'description' => $request->description,
        'is_active' => $request->has('is_active') ? $request->is_active : true, // Default to true if not provided
    ]);
}

public function updateByRequest(ProductRequest $request, Product $product): Product
{
    if ($request->hasFile('image')) {
        (new MediaRepository())->updateByRequest(
            $request->image,
            $this->path,
            'image',
            $product->thumbnail
        );
    }

    $qrcodeURl = '';

    // Determine the value for 'store_id'
    $storeId = auth()->user()->hasAnyRole(['admin', 'root'])
        ? $product->store_id // Keep the old store_id if admin or root
        : auth()->user()->store?->id; // Use the logged-in user's store_id otherwise

    $product->update([
        'name' => $request->name,
        'name_bn' => $request->name_bn,
        'slug' => $request->slug,
        'store_id' => $storeId, // Dynamically set store_id
        'service_id' => $request->service_id,
        'variant_id' => $request->variant_id,
        'discount_price' => $request->discount_price,
        'price' => $request->price,
        'description' => $request->description,
        'qrcode' => $request->qrcode,
        'qrcode_url' => $qrcodeURl,
    ]);

    return $product;
}


    public function updateStatusById(Product $product): Product
    {
        $product->update([
            'is_active' => ! $product->is_active,
        ]);

        return $product;
    }

    public function deleteProductById(Product $product): Product
    {
        $thumbnail = $product->thumbnail;
        if (Storage::exists($thumbnail->src)) {
            Storage::delete($thumbnail->src);
        }

        $thumbnail->delete();
        $product->delete();

        return $product;
    }

    public function findById($id): Product
    {
        return $this->find($id);
    }
}
