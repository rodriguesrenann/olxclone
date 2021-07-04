<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use App\Models\Ad;
use App\Models\Category;
use App\Models\State;
use App\Models\User;

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
        $array = [];
        $url = [];
        $validator = Validator::make($request->all(), [
            'images[]' => 'file|mimes:jpg,png',
            'state' => 'required|integer|exists:states,id',
            'title' => 'required|string|min:4',
            'price' => 'required',
            'description' => 'min:5|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first(),
            ], 400);
        }

        if ($request->hasFile('images')) {
            foreach ($request['images'] as $file) {
                $url = $file->store('public/ads');
                $url = explode('/', $url);
                $url = $url[2];
                $array[] = $url;
            }
        }
        $url = implode(',', $array);

        Ad::create([
            'user_id' => Auth::id(),
            'images' => $url ?? '',
            'state' => $request['state'],
            'title' => $request['title'],
            'price' => $request['price'],
            'price_negotiable' => 1,
            'description' => $request['description'],
            'created_at' => date('Y-m-d H:i:s'),
            'views' => 0,
            'status' => 1
        ]);

        return response()->json([
            'success' => 'Anuncio criado com sucesso',
        ], 200);
        //todo
    }

    public function getAds(Request $request, Ad $ad)
    {
        $ad = $ad->newQuery();

        if ($request->has('state')) {
            $ad->where('state', $request->input('state'));
        }

        if ($request->has('q')) {
            $ad->where('title', 'LIKE', '%' . $request->input('q') . '%');
        }

        if ($request->has('cat')) {
            $ad->where('cat', $request->input('cat'));
        }

        $offset = $request->has('offset') ?? 0;

        $ads = $ad->with('state')->orderBy('created_at')
            ->offset($offset)
            ->limit(5)
            ->get();

        return response()->json([
            'ads' => $ads
        ], 200);
    }

    public function getItem(Request $request, $id)
    {
        $ad = Ad::where('id', $id)->first();

        if (!$ad) {
            return response()->json([
                'error' => 'Anuncio não encontrado'
            ], 404);
        }

        //aumentar o numero de views no anuncio
        $ad->views++;
        $ad->save();

        //infos do usuario que fez o anuncio
        $user = $ad->user->first();
        $userState = State::where('id', $user['state'])->first();
        $ad['user']['state'] = $userState['name'];

        //organizr url das imagens
        $imagesArray = [];
        $images = explode(',', $ad['images']);
        foreach ($images as $img) {
            $imagesArray[] = asset('storage/ads/' . $img);
        }

        //pegar somente o nome do estado
        $state = State::where('id', $ad['state'])->first();

        $ad['images'] = $imagesArray;
        $ad['state'] = $state['name'];

        $arrayAds = [];

        if ($request->has('others')) {
            $othersAds = Ad::where('user_id', $ad['user_id'])->where('status', 1)->get();
            foreach ($othersAds as $otherAd) {
                if ($otherAd['id'] !== $ad['id']) {
                    $otherAd['images'] = asset('storage/ads/' . $otherAd['images']);
                    $arrayAds[] = $otherAd;
                }
            }
        }
        return response()->json([
            'adInfo' => $ad,
            'others' => $arrayAds
        ], 200);
    }

    public function editAd(Request $request, $id)
    {
        $ad = Ad::where('id', $id)->where('user_id', Auth::id())->first();

        if (!$ad) {
            return response()->json([
                'error' => 'Anuncio não encontrado'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'images[]' => 'file|mimes:jpg,png',
            'state' => 'integer|exists:states,id',
            'title' => 'string|min:4',
            'description' => 'min:5|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first()
            ], 400);
        }

        $ad->title = $request['title'] ?? $ad->title;
        $ad->price = $request['price'] ?? $ad->price;
        $ad->description = $request['description'] ?? $ad->description;
        $ad->status = $request['status'] ?? $ad->status;

        if ($request->hasFile('images')) {
            foreach ($request['images'] as $file) {
                $url = $file->store('public/ads');
                $url = explode('/', $url);
                $url = $url[2];
                $array[] = $url;
            }

            $ad->images = $ad->images .','.$url;
        }
        $url = implode(',', $array);

        $ad->save();

        return response()->json([
            'success' => 'Anuncio alterado'
        ], 200);
    }

    public function deleteAd($id)
    {
        $ad = Ad::where('id', $id)->where('user_id', Auth::id())->first();

        if(!$ad) {
            return response()->json([
                'error' => 'Anuncio não encontrado'
            ], 404);
        }

        $ad->delete();

        return response()->json([
            'success' => 'Anuncio deletado com sucesso'
        ], 200);
    }
}
