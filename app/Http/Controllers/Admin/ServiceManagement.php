<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Service;
use App\Models\ServiceProduct;
use App\Models\ServiceStreamer;
use App\Models\Streamer;
use App\Models\StreamerCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ServiceManagement extends Controller
{
    const ERROR_RESPONSE = 'Unable to complete your request the moment.';

    function index()
    {
        return view('admin.services.index');
    }

    function filter()
    {
        $data['services'] = Service::paginate(50);

        return view('admin.services._table', $data);
    }

    function create()
    {
        $data['products'] = Product::get();
        $data['streamers'] = Streamer::get();

        return view('admin.services.create', $data);
    }

    function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'price' => 'required|numeric|min:0',
            'bv'    => 'required|numeric|min:0',
            'duration' => 'required|numeric',
            'duration_unit' => 'required|string',
            'description' => 'nullable',
            'package_type' => 'required|in:service,ambassadorship',
            'products' => 'required_if:package_type,service|array',
            'products.*' => 'required_if:package_type,service|exists:products,id',
            'icon' => 'required|image',
            'banner' => 'required|image',
            'product_image' => 'required|image',
            'streamers' => 'nullable',
            'streamers.*' => 'required|exists:streamers,id',
        ]);

        // Handle validation errors
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {

            DB::beginTransaction();

            if ($request->hasFile('icon')) {
                $imageUrl = uploadFile($request->file('icon'), "uploads/packages/icons", "do_spaces");
                $request->merge(['image_url' => $imageUrl]);
            }

            if ($request->hasFile('banner')) {
                $bannerUrl = uploadFile($request->file('banner'), "uploads/packages/banners", "do_spaces");
                $request->merge(['banner_url' => $bannerUrl]);
            }

            if ($request->hasFile('product_image')) {
                $productImageUrl = uploadFile($request->file('product_image'), "uploads/packages/products", "do_spaces");
                $request->merge(['product_image_url' => $productImageUrl]);
            }

            $service = Service::create($this->prepareData($request));

            if ($request->package_type === "service") {
                foreach ($request->products as $product) {
                    ServiceProduct::create([
                        'service_id' => $service->id,
                        'product_id' => $product
                    ]);
                }
            }

            $service->streamers()->sync($request->streamers);

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Service created successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            logger($e);
            return response()->json(['success' => false, 'message' => serviceDownMessage()], 500);
        }
    }

    function edit(Service $service)
    {
        $data['service'] = $service;
        $data['products'] = Product::get();
        $data['streamers'] = Streamer::get();
        $data['serviceProducts'] = ServiceProduct::where('service_id', $service->id)->pluck('product_id')->toArray();

        return view('admin.services.edit', $data);
    }

    function show(Service $service)
    {
        $data['service'] = $service;

        return view('admin.services.show', $data);
    }

    function update(Request $request, Service $service)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'price' => 'required|numeric',
            'bv'    => 'required|numeric|min:0',
            'duration' => 'required|numeric',
            'duration_unit' => 'required|string',
            'description' => 'nullable',
            'package_type' => 'required|in:service,ambassadorship',
            'products' => 'required_if:package_type,service|array',
            'products.*' => 'required_if:package_type,service|exists:products,id',
            'image' => 'nullable|image',
            'banner' => 'nullable|image',
            'product_image' => 'nullable|image',
            'streamers' => 'nullable',
            'streamers.*' => 'required|exists:streamers,id',
        ]);

        // Handle validation errors
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {

            DB::beginTransaction();

            if ($request->hasFile('icon')) {
                deleteFile($service->image_url);
                $imageUrl = uploadFile($request->file('icon'), "uploads/packages/icons", "do_spaces");
                $request->merge(['image_url' => $imageUrl]);
            } else {
                $request->merge(['image_url' => $service->image_url]);
            }

            if ($request->hasFile('banner')) {
                deleteFile($service->banner);
                $bannerUrl = uploadFile($request->file('banner'), "uploads/packages/banners", "do_spaces");
                $request->merge(['banner_url' => $bannerUrl]);
            } else {
                $request->merge(['banner_url' => $service->banner]);
            }

            if ($request->hasFile('product_image')) {
                deleteFile($service->product_image);
                $productionImageUrl = uploadFile($request->file('product_image'), "uploads/packages/products", "do_spaces");
                $request->merge(['product_image_url' => $productionImageUrl]);
            } else {
                $request->merge(['product_image_url' => $service->product_image]);
            }

            $service->update($this->prepareData($request));

            if (($request->package_type === "service")) {
                ServiceProduct::where('service_id', $service->id)->delete();

                foreach ($request->products as $product) {
                    ServiceProduct::create([
                        'service_id' => $service->id,
                        'product_id' => $product
                    ]);
                }
            } else {
                ServiceProduct::where('service_id', $service->id)->delete();
            }

            $service->streamers()->sync($request->streamers);

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Service updated successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            logger($e);
            return response()->json(['success' => false, 'message' => serviceDownMessage()], 500);
        }
    }

    function publish(Service $service)
    {
        $service->update([
            'is_published' => true,
        ]);

        return response(['success' => true, 'message' => 'Publish successfully']);
    }

    function draft(Service $service)
    {
        $service->update([
            'is_published' => false,
        ]);

        return response(['success' => true, 'message' => 'Service set as draft successfully.']);
    }

    function destroy(Service $service)
    {
        ServiceProduct::where('service_id', $service->id)->delete();

        $service->delete();

        return response()->json(['success' => false, 'message' => 'Service deleted successfully.']);
    }

    function prepareData($request)
    {

        if ($request->duration_unit === "infinite") {
            $duration = 0;
        } else {
            $duration = $request->duration;
        }

        return [
            'name' => $request->name,
            'price' => $request->price,
            'bv_amount' => $request->bv,
            'duration' => $duration === 0 ? 0 : getDurationInDays($duration, $request->duration_unit),
            'duration_unit' => $request->duration_unit,
            'auto_renewal' => $request->auto_renewal === "on" ? true : false,
            'is_published' => $request->is_published === "on" ? true : false,
            'ambassadorship' => $request->package_type === "ambassadorship" ? true : false,
            'description' => $request->description,
            'image_url'  => $request->image_url,
            'banner' => $request->banner_url,
            'product_image' => $request->product_image_url
        ];
    }
}
