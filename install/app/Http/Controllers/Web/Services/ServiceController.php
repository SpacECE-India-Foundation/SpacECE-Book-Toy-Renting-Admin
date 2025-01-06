<?php

namespace App\Http\Controllers\Web\Services;

use App\Http\Controllers\Controller;
use App\Http\Requests\ServiceRequest;
use App\Models\Service;
use App\Repositories\AdditionalRepository;
use App\Repositories\ServiceRepository;
use App\Repositories\VariantRepository;

class ServiceController extends Controller
{
    public $serviceRepo;

    public function __construct(ServiceRepository $serviceRepository)
    {
        $this->serviceRepo = $serviceRepository;
    }

    public function index()
    {
        $services = $this->serviceRepo->getAll(true);

        return view('services.index', compact('services'));
    }

    public function create()
    {
        $variants = (new VariantRepository())->getAll(true);

        return view('services.create', compact('variants'));
    }

    public function store(ServiceRequest $request)
    {
        $this->serviceRepo->storeByRequest($request);

        return redirect()->route('service.index')->with('success', 'Service added success');
    }

    public function edit(Service $service)
    {
        $additionals = (new AdditionalRepository())->getAllByActive();

        return view('services.edit', compact('service', 'additionals'));
    }

    public function update(ServiceRequest $request, Service $service)
    {
        $this->serviceRepo->updateByRequest($request, $service);
        // $service->additionals()->sync($request->additional);

        return redirect()->route('service.index')->with('success', 'Service updated successfully');
    }
    public function getServicesByShop($shopId)
{
    $shop = \App\Models\Store::findOrFail($shopId); // Find the shop by ID
    $services = $shop->services; // Assuming the Store model has a relationship with Service
    return response()->json($services); // Return services as a JSON response
}

    public function toggleActivationStatus(Service $service)
    {
        $this->serviceRepo->updateStatusById($service);

        return back()->with('success', 'Status updated');
    }

    public function getVariant(Service $service)
{
    if (auth()->user()->hasAnyRole(['admin', 'root'])) {
        $variants = $service->variants()->get();
    } else {
        $variants = $service->variants()->where('store_id', auth()->user()->store?->id)->get();
    }

    return response()->json($variants);
}

}
