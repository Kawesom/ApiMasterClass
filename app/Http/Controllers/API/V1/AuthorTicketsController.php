<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Filters\V1\TicketFilter;
use App\Http\Requests\API\V1\StoreTicketsRequest;
use App\Http\Resources\V1\TicketsResource;
use App\Models\Tickets;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class AuthorTicketsController extends Controller
{
    public function index($author_id, TicketFilter $filter) {
        return TicketsResource::collection(
            Tickets::where('users_id',$author_id)
            ->filter($filter)
            ->paginate()
        );
        //to test - http://127.0.0.1:8000/api/v1/authors/5/tickets?filter[status]=Completed
        //to test - http://127.0.0.1:8000/api/v1/authors/5/tickets?filter[status]=Completed,Cancelled
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store($author_id, StoreTicketsRequest $request)
    {
        $model = [
            'title' => $request->input('data.attributes.title'),
            'description' => $request->input('data.attributes.description'),
            'status' => $request->input('data.attributes.status'),
            'users_id' => $author_id
        ];

        return new TicketsResource(Tickets::create($model));

    }
}
