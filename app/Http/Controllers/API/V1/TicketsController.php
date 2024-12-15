<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Filters\V1\TicketFilter;
use App\Http\Requests\API\V1\ReplaceTicketsRequest;
use App\Models\Tickets;
use App\Http\Requests\API\V1\StoreTicketsRequest;
use App\Http\Requests\API\V1\UpdateTicketsRequest;
use App\Http\Resources\V1\TicketsResource;
use App\Models\User;
use App\Policies\V1\TicketPolicy;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TicketsController extends ApiController
{
    protected $policyClass = TicketPolicy::class;
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

        if ($this->isAble('store', new Tickets())) {
            return new TicketsResource(Tickets::create($request->mappedAttributes()));
        }

        return $this->notAuthorized('You are not authorized to update that resource');
    }

    /**
     * Display the specified resource.
     */
    public function show(Tickets $ticket)
    {
        if ($this->include('authors')) {
            return new TicketsResource($ticket->load('users'));
        }
        return new TicketsResource($ticket);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTicketsRequest $request, Tickets $ticket)
    {
        //patch
            if ($this->isAble('update', $ticket)) {
                $ticket->update($request->mappedAttributes());

                return new TicketsResource($ticket);
            }
            return $this->notAuthorized('You are not authorized to update that resource');

    }

    public function replace(ReplaceTicketsRequest $request, Tickets $ticket)
    {
        //put
            if ($this->isAble('replace', $ticket)) {
                $ticket->update($request->mappedAttributes());

                return new TicketsResource($ticket);
            }

            return $this->notAuthorized('You are not authorized to replace that resource');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tickets $ticket)
    {
            if ($this->isAble('delete', $ticket)) {
                $ticket->delete();

                return $this->ok('Ticket Successfully deleted.');
            }

            return $this->notAuthorized('You are not authorized to delete that resource');
    }
}
