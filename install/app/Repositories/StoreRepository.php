<?php

namespace App\Repositories;

use App\Enums\Roles;
use App\Http\Requests\ShopRequest;
use App\Http\Requests\StoreUpdateRequest;
use App\Models\OrderSchedule;
use App\Models\Store;
use App\Models\Wallet;
use Spatie\Permission\Models\Role;

class StoreRepository extends Repository
{
    private $path = 'public/images/shops/';

    public function model()
    {
        return Store::class;
    }

    public function storeByRequest(ShopRequest $request): Store
    {
        $user = (new UserRepository())->registerUser($request, true);

        Wallet::create([
            'user_id' => $user->id,
        ]);

        $role = Role::where('name', Roles::STORE->value)->first();
        $permissions = $role->getPermissionNames()->toArray();
        $user->givePermissionTo($permissions);

        $user->assignRole(Roles::STORE->value);

        $logoId = $this->uploadImage($request, 'logo');
        $bannerId = $this->uploadImage($request, 'banner');

        return $this->create([
            'shop_owner' => $user->id,
            'logo_id' => $logoId,
            'banner_id' => $bannerId,
            'name' => $request->name,
            'commission' => $request->commission ?? 0,
            'description' => $request->description,
            'status' => true,
            'prifix' => $request->prefix ?? 'IM',
        ]);
    }

    public function getByNearest($data)
    {
        $service = $data->service_id;
        $search = $data->search;

        $stores = $this->query()->when($search, function ($query) use ($search) {
            $query->where('name', 'like', "%{$search}%");
        })->when($service, function ($query) use ($service) {
            $query->whereHas('services', function ($query) use ($service) {
                $query->where('id', $service);
            });
        })->whereHas('user', function ($query) {
            $query->where('is_active', true);
        })->whereNotNull('longitude')->whereNotNull('latitude')->get();

        $availableStores = $this->getInsideStoresFromPolygon($data, $stores);

        $nearest = [];
        foreach ($availableStores as $store) {
            $distance = getDistance([$data->latitude, $data->longitude], [$store->latitude, $store->longitude]);
            $nearest[(string) round($distance, 2)] = $store;
        }
        ksort($nearest);

        return $nearest;
    }

    private function getInsideStoresFromPolygon($data, $stores)
    {
        $availableStores = [];

        foreach ($stores as $store) {
            if ($store->area) {
                $boundaryAreas = [];
                foreach ($store->area->latLngs as $latlng) {
                    $boundaryAreas[] = [
                        (float) $latlng->pivot->lat,
                        (float) $latlng->pivot->lng
                    ];
                }

                // Check if the user latitude and longitude location is within the area
                $userPoint = [$data->latitude, $data->longitude];

                if ($this->isPointInPolygon($boundaryAreas, $userPoint)) {
                    $availableStores[] = $store;
                }
            } else {
                $availableStores[] = $store;
            }
        }
        return $availableStores;
    }

    private function isPointInPolygon($polygon, $point)
    {
        $verticesX = array_column($polygon, 0);
        $verticesY = array_column($polygon, 1);
        $pointsCount = count($polygon);
        $i = $j = $c = 0;

        for ($i = 0, $j = $pointsCount - 1; $i < $pointsCount; $j = $i++) {
            if (((($verticesY[$i] <= $point[1]) && ($point[1] < $verticesY[$j])) || (($verticesY[$j] <= $point[1]) && ($point[1] < $verticesY[$i]))) &&
                ($point[0] < ($verticesX[$j] - $verticesX[$i]) * ($point[1] - $verticesY[$i]) / ($verticesY[$j] - $verticesY[$i]) + $verticesX[$i])
            ) {
                $c = !$c;
            }
        }
        return $c;
    }

    private function uploadImage($request, $name): ?int
    {
        $thumbnail = null;
        if ($request->hasFile($name)) {
            $thumbnail = (new MediaRepository())->storeByRequest(
                $request->{$name},
                $this->path,
                'shop ' . $name,
                'image'
            );
            $thumbnail = $thumbnail->id;
        }

        return $thumbnail;
    }

    public function updateByRequest(ShopRequest $request, Store $store): Store
    {
        (new UserRepository())->update($store->user, [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'gender' => $request->gender,
            'mobile' => $request->mobile,
            'date_of_birth' => $request->date_of_birth ?? $store->user->date_of_birth,
        ]);

        $thumbnailLogo = $this->logoUpdate($request, $store);
        $thumbnailBanner = $this->bannerUpdate($request, $store);

        $this->update($store, [
            'logo_id' => $thumbnailLogo ? $thumbnailLogo->id : null,
            'banner_id' => $thumbnailBanner ? $thumbnailBanner->id : null,
            'name' => $request->name,
            'commission' => $request->commission,
            'description' => $request->description,
        ]);

        return $store;
    }

    public function updateOnlyStoreByRequest(StoreUpdateRequest $request, Store $store): Store
    {
        $thumbnailLogo = $this->logoUpdate($request, $store);
        $thumbnailBanner = $this->bannerUpdate($request, $store);

        $this->update($store, [
            'logo_id' => $thumbnailLogo ? $thumbnailLogo->id : null,
            'banner_id' => $thumbnailBanner ? $thumbnailBanner->id : null,
            'name' => $request->name,
            'delivery_charge' => $request->delivery_charge ?? $store->delivery_charge,
            'min_order_amount' => $request->min_order_amount ?? $store->min_order_amount,
            'description' => $request->description,
            'prifix' => $request->prefix ?? $store->prifix,
        ]);

        return $store;
    }

    public function bannerUpdate($request, $store)
    {
        $thumbnail = $store->banner;
        if ($request->hasFile('banner') && $thumbnail == null) {
            $thumbnail = (new MediaRepository())->storeByRequest(
                $request->banner,
                $this->path,
                'shop images',
                'image'
            );
        }

        if ($request->hasFile('banner') && $thumbnail) {
            $thumbnail = (new MediaRepository())->updateByRequest(
                $request->banner,
                $this->path,
                'image',
                $thumbnail
            );
        }

        return $thumbnail;
    }

    public function logoUpdate($request, $store)
    {
        $thumbnail = $store->logo;
        if ($request->hasFile('logo') && $thumbnail == null) {
            $thumbnail = (new MediaRepository())->storeByRequest(
                $request->logo,
                $this->path,
                'shop images',
                'image'
            );
        }

        if ($request->hasFile('logo') && $thumbnail) {
            $thumbnail = (new MediaRepository())->updateByRequest(
                $request->logo,
                $this->path,
                'image',
                $thumbnail
            );
        }

        return $thumbnail;
    }

    public function createSchedule($store)
    {
        $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        foreach ($days as $day) {
            OrderSchedule::create([
                'store_id' => $store->id,
                'day' => $day,
                'start_time' => 8,
                'end_time' => 16,
                'per_hour' => 1,
                'is_active' => true,
                'type' => 'pickup',
            ]);
        }

        foreach ($days as $day) {
            OrderSchedule::create([
                'store_id' => $store->id,
                'day' => $day,
                'start_time' => 8,
                'end_time' => 16,
                'per_hour' => 1,
                'is_active' => true,
                'type' => 'delivery',
            ]);
        }
    }
}
