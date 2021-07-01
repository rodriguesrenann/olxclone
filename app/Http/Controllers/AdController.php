<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Ad;

class AdController extends Controller
{
    public function getCategories()
    {
        $categories = Category::all();

        foreach ($categories as $cat) {
            $cat['img'] = asset('storage/images/' . $cat['slug'] . '.png'); //cria um novo indice em cada elemento chamado img
        }

        return response()->json([
            'data' => $categories,
        ]);
    }

    
    public function newAd(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'file|mimes:jpg,png',
            'state' => 'required|integer|exists:states,id',
            'title' => 'required|string|min:4',
            'price' => 'required|numeric',
            'description' => 'min:5|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first(),
            ], 400);
        }

        if ($request->hasFile('image')) {
            foreach ($request->allFiles() as $file) {
                $file->store('public/ads');
                $url = asset(Storage::url($file));
            }
        }

        Ad::create([
            'user_id' => Auth::id(),
            'images' => $url??'',
            'state'=> $request['state'],
            'title' => $request['title'],
            'price' => $request['price'],
            'price_negotiable' => 1,
            'description' => $request['description'],
            'created_at' => date('Y-m-d H:i:s'),
            'views' => 0,
            'status' => 1
        ]);
    }

    //todo
}
