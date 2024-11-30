<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Tickets;
use App\Http\Requests\API\V1\StoreTicketsRequest;
use App\Http\Requests\API\V1\UpdateTicketsRequest;
use App\Http\Resources\V1\TicketsResource;

class TicketsController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return TicketsResource::collection(Tickets::paginate());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTicketsRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Tickets $ticket)
    {
        if($this->include('author')) {
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
