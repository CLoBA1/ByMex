<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::orderBy('sort_order')->get();
        return view('admin.banners.index', compact('banners'));
    }

    public function create()
    {
        return view('admin.banners.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'image_desktop' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'image_mobile' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:1000',
            'link' => 'nullable|string|max:255',
            'button_text' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer',
        ]);

        $data = $request->except(['image_desktop', 'image_mobile']);
        $data['is_active'] = $request->has('is_active');
        $data['sort_order'] = $request->sort_order ?? 0;

        if ($request->hasFile('image_desktop')) {
            $data['image_desktop'] = $request->file('image_desktop')->store('banners', 'public');
        }

        if ($request->hasFile('image_mobile')) {
            $data['image_mobile'] = $request->file('image_mobile')->store('banners', 'public');
        }

        Banner::create($data);

        return redirect()->route('admin.banners.index')->with('success', 'Banner creado exitosamente.');
    }

    public function edit(Banner $banner)
    {
        return view('admin.banners.edit', compact('banner'));
    }

    public function update(Request $request, Banner $banner)
    {
        $request->validate([
            'image_desktop' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'image_mobile' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:1000',
            'link' => 'nullable|string|max:255',
            'button_text' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer',
        ]);

        $data = $request->except(['image_desktop', 'image_mobile']);
        $data['is_active'] = $request->has('is_active');
        $data['sort_order'] = $request->sort_order ?? 0;

        if ($request->hasFile('image_desktop')) {
            if ($banner->image_desktop) {
                Storage::disk('public')->delete($banner->image_desktop);
            }
            $data['image_desktop'] = $request->file('image_desktop')->store('banners', 'public');
        }

        if ($request->hasFile('image_mobile')) {
            if ($banner->image_mobile) {
                Storage::disk('public')->delete($banner->image_mobile);
            }
            $data['image_mobile'] = $request->file('image_mobile')->store('banners', 'public');
        }

        $banner->update($data);

        return redirect()->route('admin.banners.index')->with('success', 'Banner actualizado exitosamente.');
    }

    public function destroy(Banner $banner)
    {
        if ($banner->image_desktop) {
            Storage::disk('public')->delete($banner->image_desktop);
        }
        if ($banner->image_mobile) {
            Storage::disk('public')->delete($banner->image_mobile);
        }
        $banner->delete();

        return redirect()->route('admin.banners.index')->with('success', 'Banner eliminado exitosamente.');
    }
}
