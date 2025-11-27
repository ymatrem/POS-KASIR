<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::with('category')->paginate(10);
        return view('menus.index', compact('menus'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('menus.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image_url' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
        ]);

        // Handle file upload - if image uploaded, use uploaded image URL
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('menus', 'public');
            $validated['image_url'] = Storage::url($path);
        } else {
            // If no file uploaded, remove image_url from validated data (it will be null)
            unset($validated['image_url']);
        }

        // Remove image key (it's only for file upload, not stored in DB)
        unset($validated['image']);

        Menu::create($validated);
        return redirect()->route('menus.index')->with('success', 'Menu berhasil ditambahkan');
    }

    public function edit(Menu $menu)
    {
        $categories = Category::all();
        return view('menus.edit', compact('menu', 'categories'));
    }

    public function update(Request $request, Menu $menu)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'image_url' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
        ]);

        // Handle file upload - if image uploaded, use uploaded image URL
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('menus', 'public');
            $validated['image_url'] = Storage::url($path);
        } else {
            // If no file uploaded, don't update image_url (keep existing or set to null)
            unset($validated['image_url']);
        }

        // Remove image key (it's only for file upload, not stored in DB)
        unset($validated['image']);

        $menu->update($validated);
        return redirect()->route('menus.index')->with('success', 'Menu berhasil diperbarui');
    }

    public function destroy(Menu $menu)
    {
        $menu->delete();
        return redirect()->route('menus.index')->with('success', 'Menu berhasil dihapus');
    }

    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $path = $request->file('image')->store('menus', 'public');
        
        return response()->json([
            'success' => true,
            'url' => Storage::url($path),
            'path' => $path,
        ]);
    }
}

