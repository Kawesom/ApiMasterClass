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
     * Get all users
     *
     * @group Managing Users
     *
     * @queryParam sort string Data field(s) to sort by. Separate multiple fields with commas. Denote descending sort with a minus sign. Example: sort=name
     * @queryParam filter[name] Filter by status name. Wildcards are supported. No-example
     * @queryParam filter[email] Filter by email. Wildcards are supported. No-example
     *
     */
    public function index(AuthorFilter $filters)
    {
            return UsersResource::collection(User::filter($filters)->paginate());
    }

    /**
     * Create a user
     *
     * @group Managing Users
     *
     * @response 200 {"data":{"type":"user","id":16,"attributes":{"name":"My User","email":"user@user.com","isManager":false},"links":{"self":"http:\/\/localhost:8000\/api\/v1\/authors\/16"}}}
     */
    public function store(StoreUserRequest $request)
    {

        if ($this->isAble('store', new User())) {
            return new UsersResource(User::create($request->mappedAttributes()));
        }

        return $this->notAuthorized('You are not authorized to create that resource');
    }

    /**
     * Display a user
     *
     * @group Managing Users
     *
     *
     */
    public function show(User $user)
    {
        if ($this->include('tickets')) {
            return new UsersResource($user->load('tickets'));
        }
        return new UsersResource($user);
    }

    /**
     * Replace a user
     *
     * @group Managing Users
     *
     * @response 200 {"data":{"type":"user","id":16,"attributes":{"name":"My User","email":"user@user.com","isManager":false},"links":{"self":"http:\/\/localhost:8000\/api\/v1\/authors\/16"}}}
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
     * Update a user
     *
     * @group Managing Users
     *
     * @response 200 {"data":{"type":"user","id":16,"attributes":{"name":"My User","email":"user@user.com","isManager":false},"links":{"self":"http:\/\/localhost:8000\/api\/v1\/authors\/16"}}}
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
     * Delete a user
     *
     * @group Managing Users
     *
     * @response 200 {}
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
