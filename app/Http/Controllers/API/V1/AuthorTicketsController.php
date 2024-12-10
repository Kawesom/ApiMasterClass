<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Filters\V1\TicketFilter;
use App\Http\Requests\API\V1\ReplaceTicketsRequest;
use App\Http\Requests\API\V1\StoreTicketsRequest;
use App\Http\Requests\API\V1\UpdateTicketsRequest;
use App\Http\Resources\V1\TicketsResource;
use App\Models\Tickets;
use App\Models\User;
use App\Traits\ApiResponses;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class AuthorTicketsController extends ApiController
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
        return new TicketsResource(Tickets::create($request->mappedAttributes()));
    }

    public function update(UpdateTicketsRequest $request, $author_id,  $ticket_id) {
        // PUT
        try {
            $ticket = Tickets::findOrFail($ticket_id);

            if ($ticket->users_id == $author_id) {
                $ticket->update($request->mappedAttributes());
                return new TicketsResource($ticket);
            }
            // TODO: ticket doesn't belong to user

        } catch (ModelNotFoundException $exception) {
            return $this->error('Ticket cannot be found.',[], 404);
        }
    }

    public function replace(ReplaceTicketsRequest $request, $author_id, $ticket_id)
    {
        try {
            $ticket = Tickets::findOrFail($ticket_id);

            if ($author_id == $ticket->users_id) {

                $ticket->update($request->mappedAttributes());

                return new TicketsResource($ticket);
            }
            // TODO: Ticket doesn't belong to user
        } catch (ModelNotFoundException $exception) {
            return $this->error("Ticket Cannot Be Found", [], 404);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($author_id, $ticket_id)
    {
        try {
            $ticket = Tickets::findOrFail($ticket_id);

            if ($author_id == $ticket->users_id) {
                $ticket->delete();
                return $this->ok('Ticket Successfully deleted.');
            }
            return $this->error("Ticket Cannot Be Found", [], 404);

        } catch (ModelNotFoundException $exception) {
            return $this->error("Ticket Cannot Be Found", [], 404);
        }
    }
}
