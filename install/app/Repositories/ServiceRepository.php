<?php

namespace App\Repositories;

use App\Http\Requests\ServiceRequest;
use App\Models\Service;

class ServiceRepository extends Repository
{
    private $path = 'public/images/services/';

    public function model()
    {
        return Service::class;
    }

    public function getAll($isLatest = false)
    {
        $services = $this->query();
        if ($isLatest) {
            $services->latest('id');
        }

        return $services->get();
    }

    public function getActiveServices()
    {
        return $this->query()->isActive()->get();
    }

    public function storeByRequest(ServiceRequest $request): Service
    {
        $thumbnail = (new MediaRepository())->storeByRequest(
            $request->image,
            $this->path,
            'this image for service thumbnail',
            'image'
        );

        return $this->create([
            'name' => $request->name,
            'name_bn' => $request->name_bn,
            'description' => $request->description,
            'description_bn' => $request->description_bn,
            'thumbnail_id' => $thumbnail->id,
        ]);
    }

    public function updateByRequest(ServiceRequest $request, Service $service): Service
    {

        if ($request->hasFile('image')) {
            (new MediaRepository())->updateByRequest(
                $request->image,
                $this->path,
                'image',
                $service->thumbnail
            );
        }

        $this->update($service, [
            'name' => $request->name,
            'name_bn' => $request->name_bn,
            'description' => $request->description,
            'description_bn' => $request->description_bn,
        ]);

        return $service;
    }

    public function updateStatusById(Service $service): Service
    {
        $service->update([
            'is_active' => ! $service->is_active,
        ]);

        return $service;
    }

    public function findOrFailById($serviceId): Service
    {
        $service = $this->model()::findOrFail($serviceId);

        return $service;
    }
}
