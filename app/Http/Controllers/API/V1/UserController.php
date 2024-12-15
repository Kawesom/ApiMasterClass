<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Filters\V1\AuthorFilter;
use App\Http\Requests\API\V1\ReplaceUserRequest;
use App\Models\User;
use App\Http\Requests\API\V1\StoreUserRequest;
use App\Http\Requests\API\V1\UpdateUserRequest;
use App\Http\Resources\V1\UsersResource;
use App\Policies\V1\UserPolicy;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserController extends ApiController
{
    protected $policyClass = UserPolicy::class;
    /**
     * Display a listing of the resource.
     */
    public function index(AuthorFilter $filters)
    {
            return UsersResource::collection(User::filter($filters)->paginate());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {

        if ($this->isAble('store', new User())) {
            return new UsersResource(User::create($request->mappedAttributes()));
        }

        return $this->notAuthorized('You are not authorized to create that resource');
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
    public function replace(ReplaceUserRequest $request, User $user)
    {
        //put request

            if ($this->isAble('replace', $user)) {
                $user->update($request->mappedAttributes());

                return new UsersResource($user);
            }

            return $this->notAuthorized('You are not authorized to replace that resource');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        //patch

            if ($this->isAble('update', $user)) {
                $user->update($request->mappedAttributes());

                return new UsersResource($user);
            }

            return $this->notAuthorized('You are not authorized to update that resource');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {

            if ($this->isAble('delete', $user)) {
                $user->delete();

                return $this->ok('User Successfully deleted.');
            }

            return $this->notAuthorized('You are not authorized to delete that resource');
    }
}
