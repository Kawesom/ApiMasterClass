<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Requests\API\V1\StoreUserRequest;
use App\Http\Requests\API\V1\UpdateUserRequest;
use App\Http\Resources\V1\UsersResource;

class UsersController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if ($this->include('tickets')) {
            return UsersResource::collection(User::with('tickets')->paginate());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        if ($this->include('tickets')) {
            return new UsersResource($user->load('tickets'));
        }
        return new UsersResource($user);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }
}
