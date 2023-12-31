<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchPokemonRequest;
use App\Http\Requests\StorePokemonRequest;
use App\Http\Requests\UpdatePokemonRequest;
use App\Http\Resources\PokemonResource;
use App\Models\Nature;
use App\Models\Pokemon;
use App\Models\Race;
use App\Models\Skill;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;



class PokemonController extends Controller
{

    /**
     * Create a new user.
     *
     * This endpoint allows you to show all pokemons.
     *
     * @group Pokemons
     * @authenticated
     *
     * @bodyParam first_name string required The first name of the user.
     * @bodyParam last_name string required The last name of the user.
     * @bodyParam email string required The email address of the user.
     */


    public function index()
    {
        // 透過JWT取得當前登入的用戶
        $user = auth()->user();
        $pokemons = Pokemon::with(['race', 'user', 'ability','nature'])
        ->where('user_id', $user->id)->get();

//user->post->name// weher 一筆一筆  user::with(post)->name// whereIn
    return PokemonResource::collection($pokemons);

        // 可能可以用user角度去對關聯拿資料

        // 只獲取當前登入用戶的寶可夢
        // $pokemons = Pokemon::with(['race', 'ability', 'nature', 'user','skills'])
        //     ->where('user_id', $user->id)
        //     ->get();

        //找出user底下有什麼寶可夢
        // $pokemons = Pokemon::with(['race', 'ability', 'nature', 'user'])
        //     ->where('user_id', $user->id);
        // $pokemonSkill = $pokemons->pluck('skills');
        // // implode(',',$array);
        // dd($pokemonSkill);
        // $arr =[];
        // foreach($pokemonSkill as $index => $skill){
        //     //    $x = Skill::whereIn('id', $skill)->get()->toArray();
        //     $x = Skill::whereIn('id', $skill)->get();
        // //    dd($x->name);
        //     $arr = $x[$index];
        //     dd($arr);

        //     // dd($x[1]['name']);
        //     // $arr[] =$x;
        //     // $x[1]['name'];
        // }

       

        // $pokemons = $user->pokemons()->with(['race', 'ability', 'nature', 'skills'])->get();


        // dd($pokemons);
        // $pokemonSkill = $pokemons->pluck('skills');
        //         // dd($pokemonSkill);
        // dd($pokemonSkill);
        //         $arr = [];
        // foreach ($pokemonSkill as $index => $skill) {
        //     // $skillsNames = Skill::whereIn('id', $skill)->pluck('name')->toArray();
        //     $skillsNames = Skill::whereIn('id', $skill)->pluck('name');
        //     // $skillsNames = Skill::whereIn('id', $skill)->get('name');

        //     // return PokemonResource::collection($skillsNames);

        //     $arr[] = $skillsNames;
        // }

        // $combined = array_merge(["skills" => $arr], $pokemons);

        // return PokemonResource::collection($combined->get());//->additional(['skills' =>$arr]);

    }


    // 寶可夢新增
    public function store(StorePokemonRequest $request)
    {
        // 確認目前登入者操作權限
        // authorize 為底層有去引用Illuminate\Foundation\Auth\Access\AuthorizesRequests trait
        // 此方法通常會搭配policy用,後面參數傳入以註冊之model,然後就可以對應到該model設置的判斷權限方法
        $this->authorize('create', Pokemon::class); // "App\Models\Pokemon"  //App/policy/Pokemon


        // 用validated()方法只返回在 Form Request 中定義的驗證規則對應的數據
        $validatedData = $request->toArray();

        // 要如何在該陣列加入當前使用者的id
        $userId = Auth::user()->id;
        $validatedData['user_id'] = $userId;
        $createdData = Pokemon::create($validatedData);

        return PokemonResource::make($createdData);
    }



    // 寶可夢資料修改
    public function update(UpdatePokemonRequest $request, Pokemon $pokemon)
    {
        // 你不能去修改別人的神奇寶貝
        $this->authorize('update', $pokemon); //path:Model/pokemon-> path:model->policy
        $pokemon->update($request->toArray());
        return PokemonResource::make($pokemon);
    }


    public function show(Pokemon $pokemon)
    {
        $this->authorize('show', $pokemon);
        return PokemonResource::make($pokemon);
    }



    public function destroy(Pokemon $pokemon)
    {
        $this->authorize('delete', $pokemon);
        // 刪除該寶可夢
        $pokemon->delete();

        // 返回成功響應
        return response()->json(['message' => 'Pokemon deleted successfully'], 200);
    }



    public function evolution(Pokemon $pokemon)
    {

        $this->authorize('evolution', $pokemon);
        // 拿到寶可夢進化等級
        $pokemon->load('race');
        // $pokemon = Pokemon::with('race')->find($id);
        // 取得這隻寶可夢的進化等級
        $evolutionLevel = $pokemon->race->evolution_level;

        try {
            if (!$evolutionLevel) {
                throw new Exception("寶可夢已是最終形態");
            }

            // 因為id有照順序排所以通常id+1就會是他進化的種族的id
            if ($pokemon->level > $evolutionLevel) {
                $pokemon->update(['race_id' => $pokemon->race_id + 1]);
                return response(['message' => "This Pokemon evolves."], 200);
            }

            throw new Exception("寶可夢未達進化條件");
        } catch (Exception $e) {
            return response(['message' => $e->getMessage()], 400);
        }
    }

    public function search(SearchPokemonRequest $request)
    {
        $query = Pokemon::query();
        $name = $request->input('name');
        $nature_id = $request->input('nature_id');
        $ability_id = $request->input('ability_id');
        $level = $request->input('level');
        $race_id = $request->input('race_id');

        // 如果有提供名稱，則增加名稱的搜尋條件
        if ($name) {
            $query->where('name', 'LIKE', "%$name%");
        }

        // 如果有提供性格 ID，則增加性格的搜尋條件
        if ($nature_id) {
            $query->where('nature_id', $nature_id);
        }

        if ($ability_id) {
            $query->where('ability_id', $ability_id);
        }

        if ($level) {
            $query->where('level', $level);
        }

        if ($race_id) {
            $query->where('race_id', $race_id);
        }

        // $pokemons =  $query->with(['race', 'ability', 'nature'])
        //     ->orWhere('name', 'LIKE', '%' . $name . '%')
        //     ->orWhere('nature_id', $natureId)
        //     ->get();
        $pokemons = $query->get();
        return PokemonResource::collection($pokemons);
    }
}
