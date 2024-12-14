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
        try {
            $this->isAble('store',new User());

            return new UsersResource(User::create($request->mappedAttributes()));

        } catch (AuthorizationException $exception) {
            return $this->error('You are not authorized to create that resource',[], 403);
        }
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
    public function replace(ReplaceUserRequest $request, $user_id)
    {
        //put
        try {
            $user = User::findOrFail($user_id);

            $this->isAble('replace',$user);

            $user->update($request->mappedAttributes());

            return new UsersResource($user);

        } catch (ModelNotFoundException $exception) {
            return $this->error("User Cannot Be Found", [], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, $user_id)
    {
        //patch
        try {
            $user = User::findOrFail($user_id);

            $this->isAble('update',$user);

            $user->update($request->mappedAttributes());

            return new UsersResource($user);

        } catch (ModelNotFoundException $exception) {
            return $this->error("User Cannot Be Found", [], 404);
        } catch (AuthorizationException $exception) {
            return $this->error('You are not authorized to update that resource',[], 403);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($user_id)
    {
        try {
            $user = User::findOrFail($user_id);

            $this->isAble('delete',$user);

            $user->delete();

            return $this->ok('User Successfully deleted.');
        } catch (ModelNotFoundException $exception) {
            return $this->error("User Cannot Be Found",[],404);
        }
    }
}
