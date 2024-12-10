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
        try {
            $user = User::findOrFail($request->input('data.relationships.author.data.id'));
        }catch(ModelNotFoundException $exception) {
            return $this->ok('User Not Found',[
                'error' => 'The provided user id does not exist.'
            ]);//$exception->getMessage();
        }

        return new TicketsResource($request->mappedAttributes());

    }

    /**
     * Display the specified resource.
     */
    public function show($ticket_id)
    {
        try {
            $ticket = Tickets::findOrFail($ticket_id);

            if ($this->include('authors')) {
                return new TicketsResource($ticket->load('users'));
            }
            return new TicketsResource($ticket);

        } catch (ModelNotFoundException $exception) {
            return $this->error("Ticket Cannot Be Found", [], 404);
        }

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTicketsRequest $request, $ticket_id)
    {
        //patch
        try {
            $ticket = Tickets::findOrFail($ticket_id);

            $this->isAble('update',$ticket);

            $ticket->update($request->mappedAttributes());

            return new TicketsResource($ticket);

        } catch (ModelNotFoundException $exception) {
            return $this->error("Ticket Cannot Be Found", [], 404);
        } catch (AuthorizationException $exception) {
            return $this->error('You are not authorized to update that resource',[], 403);
        }
    }

    public function replace(ReplaceTicketsRequest $request, $ticket_id)
    {
        //put
        try {
            $ticket = Tickets::findOrFail($ticket_id);

            $ticket->update($request->mappedAttributes());

            return new TicketsResource($ticket);

        } catch (ModelNotFoundException $exception) {
            return $this->error("Ticket Cannot Be Found", [], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($ticket_id)
    {
        try {
            $ticket = Tickets::findOrFail($ticket_id);
            $ticket->delete();

            return $this->ok('Ticket Successfully deleted.');
        } catch (ModelNotFoundException $exception) {
            return $this->error("Ticket Cannot Be Found",[],404);
        }
    }
}
