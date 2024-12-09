<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Filters\V1\TicketFilter;
use App\Models\Tickets;
use App\Http\Requests\API\V1\StoreTicketsRequest;
use App\Http\Requests\API\V1\UpdateTicketsRequest;
use App\Http\Resources\V1\TicketsResource;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TicketsController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index(TicketFilter $filters)
    {
        return TicketsResource::collection(Tickets::filter($filters)->paginate());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTicketsRequest $request)
    {
        try {
            $user = User::findOrFail($request->input('data.relationships.author.data.id'));
        }catch(ModelNotFoundException $exception) {
            return $this->ok('User Not Found',[
                'error' => 'The provided user id does not exist.'
            ]);//$exception->getMessage();
        }

        $model = [
            'title' => $request->input('data.attributes.title'),
            'description' => $request->input('data.attributes.description'),
            'status' => $request->input('data.attributes.status'),
            'users_id' => $request->input('data.relationships.author.data.id')
        ];

        return new TicketsResource(Tickets::create($model));

    }

    /**
     * Display the specified resource.
     */
    public function show(Tickets $ticket)
    {
        if($this->include('authors')) {
            return new TicketsResource($ticket->load('users'));
        }
        return new TicketsResource($ticket);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTicketsRequest $request, Tickets $ticket)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tickets $ticket)
    {
        //
    }
}
