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
use App\Policies\V1\TicketPolicy;
use App\Traits\ApiResponses;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class AuthorTicketsController extends ApiController
{
    protected $policyClass = TicketPolicy::class;

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
    public function store(StoreTicketsRequest $request, $author_id)
    {
        try {
            //policy
            $this->isAble('store',new Tickets());

            return new TicketsResource(Tickets::create($request->mappedAttributes([
                'author' => 'users_id'
            ])));

        } catch (AuthorizationException $exception) {
            return $this->error($request->user()->name.' ,you are not authorized to create that resource',[], 403);
        }
    }

    public function update(UpdateTicketsRequest $request, $author_id,  $ticket_id) {
        // PUT
        try {
            $ticket = Tickets::where('id', $ticket_id)
                ->where('users_id', $author_id)
                ->firstOrFail();

            $this->isAble('update', $ticket);

            $ticket->update($request->mappedAttributes());
            return new TicketsResource($ticket);

        } catch (ModelNotFoundException $exception) {
            return $this->error($request->user()->name.', that ticket cannot be found.',[], 404);
        } catch (AuthorizationException $exception) {
            return $this->error($request->user()->name.', you are not authorized to update that resource',[], 403);
        }
    }

    public function replace(ReplaceTicketsRequest $request, $author_id, $ticket_id)
    {//PATCH
        try {
            $ticket = Tickets::where('id', $ticket_id)
                ->where('users_id', $author_id)
                ->firstOrFail();

            $this->isAble('replace', $ticket);

            $ticket->update($request->mappedAttributes());

            return new TicketsResource($ticket);
        } catch (ModelNotFoundException $exception) {
            return $this->error($request->user()->name." ,that ticket cannot be found", [], 404);
        } catch (AuthorizationException $exception) {
            return $this->error($request->user()->name.', you are not authorized to replace that resource',[], 403);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($author_id, $ticket_id)
    {
        try {
            $ticket = Tickets::where('id', $ticket_id)
                            ->where('users_id', $author_id)
                            ->firstOrFail();

            $this->isAble('delete', $ticket);

            $ticket->delete();
            return $this->ok('Ticket Successfully deleted.');

        } catch (ModelNotFoundException $exception) {
            return $this->error("Ticket Cannot Be Found", [], 404);
        } catch (AuthorizationException $exception) {
            return $this->error('You are not authorized to delete that resource.',[], 403);
        }
    }
}
