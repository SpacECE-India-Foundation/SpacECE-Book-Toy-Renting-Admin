<?php

namespace App\Repositories;

use App\Http\Requests\BannerRequest;
use App\Models\Banner;
use Illuminate\Support\Facades\Storage;

class BannerRepository extends Repository
{
    private $path = 'public/images/banners/';

    public function model()
    {
        return Banner::class;
    }

    public function getAllByStatus($status)
    {
        return $this->query()->where('is_banner', $status)->get();
    }

    public function getByBannerStatus($status)
    {
        return $this->query()->where('is_banner', $status)->isActive()->get();
    }

    public function storeByRequest(BannerRequest $request): Banner
    {
        $thumbnail = (new MediaRepository())->storeByRequest(
            $request->image,
            $this->path,
            'this image for website slider banner',
            'image'
        );

        return $this->create([
            'title' => $request->title,
            'description' => $request->description,
            'thumbnail_id' => $thumbnail->id,
            'is_active' => $request->active ?? 0,
            'is_banner' => $request->banner ?? 0,
        ]);
    }

    public function updateByRequest(BannerRequest $request, Banner $banner): Banner
    {
        if ($request->hasFile('image')) {
            (new MediaRepository())->updateByRequest(
                $request->image,
                $this->path,
                'image',
                $banner->thumbnail
            );
        }

        $banner->update([
            'title' => $request->title,
            'description' => $request->description,
            'is_active' => $request->active ?? 0,
        ]);

        return $banner;
    }

    public function deleteByRequest(Banner $banner)
    {
        $thumbnail = $banner->thumbnail;
        if (Storage::exists($thumbnail->src)) {
            Storage::delete($thumbnail->src);
        }

        $banner->delete();
        $thumbnail->delete();

    }

    public function toggleActivation(Banner $banner): Banner
    {
        $banner->update([
            'is_active' => ! $banner->is_active,
        ]);

        return $banner;
    }
}
