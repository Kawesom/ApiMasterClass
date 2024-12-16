<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Filters\V1\AuthorFilter;
use App\Models\User;
use App\Http\Requests\API\V1\StoreUserRequest;
use App\Http\Requests\API\V1\UpdateUserRequest;
use App\Http\Resources\V1\UsersResource;

class AuthorsController extends ApiController
{
    /**
     * Get authors.
     *
     * Retrieves all users that created a ticket.
     *
     * @group Showing Authors
     */
    public function index(AuthorFilter $filters)
    {
            return UsersResource::collection(User::select('users.*')
            ->join('tickets', 'users.id', '=', 'tickets.users_id')
            ->filter($filters)
            ->distinct()
            ->paginate());
    }

    /**
     * Get an author.
     *
     * Retrieves all users that created a ticket.
     *
     * @group Showing Authors
     */
    public function show(User $author)
    {
        if ($this->include('tickets')) {
            return new UsersResource($author->load('tickets'));
        }
        return new UsersResource($author);
    }

}
