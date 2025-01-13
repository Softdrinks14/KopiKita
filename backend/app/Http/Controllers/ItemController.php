<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:100',
            'price' => 'required|integer',
            'image_file' => 'nullable|mimes:jpg,png',
            'category' => 'required|max:100',
        ]);

        if ($request->file('image_file')) {
            $file = $request->file('image_file');
            $fileName = $file->getClientOriginalName();
            $newName = Carbon::now()->timestamp . '_' . $fileName;

            Storage::disk('public')->putFileAs('items', $file, $newName);

            $request['image'] = $newName;
        }

        $item = Item::create($request->all());

        return response(['data' => $item]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|max:100',
            'price' => 'required|integer',
            'image_file' => 'nullable|mimes:jpg,png,JPEG,PNG',
            'category' => 'required|max:255',
        ]);

        $item = Item::find($id);

        if (!$item) {
            return response(['message' => 'Item not found'], 404);
        }

        if ($request->file('image_file')) {
            $file = $request->file('image_file');
            $fileName = $file->getClientOriginalName();
            $newName = Carbon::now()->timestamp . '_' . $fileName;

            Storage::disk('public')->putFileAs('items', $file, $newName);

            $request['image'] = $newName;
        }

        $item->update($request->all());

        return response(['data' => $item]);
    }

    public function index()
    {
        $items = Item::select('id', 'name', 'price', 'category', 'image')->get();

        return response(['data' => $items]);
    }


    public function destroy($id)
    {
        $item = Item::find($id);

        if (!$item) {
            return response(['message' => 'Item not found'], 404);
        }

        // Delete the associated image file if it exists
        if ($item->image) {
            Storage::disk('public')->delete('items/' . $item->image);
        }

        // Delete the item from database
        $item->delete();

        return response(['message' => 'Item deleted successfully']);
    }
    //     public function getImages($filename)
    //     {
    //         $images = 
    //     }
}
