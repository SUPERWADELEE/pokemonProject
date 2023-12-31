<?php

namespace App\Http\Controllers;

use App\Models\Nature;
use Illuminate\Http\Request;

class NatureController extends Controller
{
    // 性格詳情
    public function index()
    {
        $allNatures = Nature::all();
        return $allNatures;
    }


    // 性格新增
    public function store(Request $request)
    {
        $validationData = $request->validate([
            'name' => 'required|max:255',
        ]);

        Nature::create([
            'name' => $validationData['name']
        ]);

        return response(['message' => 'Nature saved successfully'], 201);
    }


    // 性格修改
    public function update(Request $request, Nature $nature)
    {
        $validationData = $request->validate([
            'name' => 'required|max:255',
        ]);

        $nature->update([
            'name' => $validationData['name']
        ]);

        return response(['message' => 'Nature updated successfully'], 200);
    }
}
