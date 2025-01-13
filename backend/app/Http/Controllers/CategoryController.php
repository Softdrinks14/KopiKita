<?php

namespace App\Http\Controllers;

use App\Models\category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'category_name' => 'required|max:100',
        ]);

        $category = category::create($request->all());

        return response(['data' => $category]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'category_name' => 'required|max:100',
        ]);

        $category = category::find($id);

        if (!$category) {
            return response(['message' => 'Category not found'], 404);
        }

        $category->update($request->all());

        return response(['data' => $category]);
    }

    public function index()
    {
        $category = category::select('id','category_name')->get();
        return response(['data' => $category]);
    }
}
