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
     * Get All Tickets
     *
     * @group Managing Tickets
     * @queryParam sort string Data field(s) to sort by. Separate multiple fields with commas(','). Denote Descending sort with a minus sign("-") at the start of each param. Example: sort=title,-createdAt
     * @queryParam filter[status] Filter by status code: Active, Completed, Hold, Cancelled. No Example
     * @queryParam filter[title] Filter by title: Wildcards are supported. Example: *fix*
     */
    public function index(TicketFilter $filters)
    {
        return TicketsResource::collection(Tickets::filter($filters)->paginate());
    }

    /**
     * Create A Ticket
     *
     * Users can only create tickes for themselves. Managers can create tickets for any user.
     *
     * @group Managing Tickets
     *
     */
    public function store(StoreTicketsRequest $request)
    {

        if ($this->isAble('store', new Tickets())) {
            return new TicketsResource(Tickets::create($request->mappedAttributes()));
        }

        return $this->notAuthorized('You are not authorized to update that resource');
    }
    /**
     * Show a specific ticket.
     *
     * Display an individual ticket.
     *
     * @group Managing Tickets
     *
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
     * Update Ticket
     *
     * Update the specified ticket in storage.
     *
     * @group Managing Tickets
     *
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

    /**
     * Replace Ticket
     *
     * Replace the specified ticket in storage.
     *
     * @group Managing Tickets
     *
     */
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
     * Delete ticket.
     *
     *
     * @group Managing Tickets
     *
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
