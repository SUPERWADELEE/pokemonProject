<?php

namespace App\Policies;

use App\Models\Pokemon;
use App\Models\User;

class PokemonPolicy
{
    protected $policies = [
        Pokemon::class => PokemonPolicy::class,
    ];

    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function index(User $user)
    {
        // 所有角色都能查看
        return in_array($user->role, ['superadmin', 'user', 'guest']);
    }


    public function show(User $user, Pokemon $pokemon)
    {
        return in_array($user->role, ['superadmin', 'user', 'guest']);
    }

    public function create(User $user)
    {
        // 只有超級管理員和一般使用者可以建立
        // dd($user->role);
        return in_array($user->role, ['superadmin', 'user']);
    }



    public function update(User $user, Pokemon $pokemon)
    {
        return in_array($user->role, ['superadmin', 'user']);
    }


    public function delete(User $user, Pokemon $pokemon)
    {
        return $user->role === 'superadmin'; // 只有超級管理員可以刪除
    }

    
    public function suspend(User $user)
    {
        return $user->role === 'superadmin'; // 只有超級管理員可以停權
    }
}