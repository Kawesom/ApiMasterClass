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
    public function store(StoreTicketsRequest $request, User $author)
    {
        //policy
        if ($this->isAble('store', new Tickets())) {
            return new TicketsResource(Tickets::create($request->mappedAttributes([
                'author' => 'users_id'
            ])));
        }

        return $this->notAuthorized($request->user()->name . ' ,you are not authorized to create that resource');
    }

    public function update(UpdateTicketsRequest $request, User $author, Tickets $ticket)
    {
        // PUT

            if ($this->isAble('update', $ticket)) {
                $ticket->update($request->mappedAttributes());
                return new TicketsResource($ticket);
            }

            return $this->notAuthorized($request->user()->name . ', you are not authorized to update that resource');
    }

    public function replace(ReplaceTicketsRequest $request, User $author, Tickets $ticket)
    { //PATCH

            if ($this->isAble('replace', $ticket)) {
                $ticket->update($request->mappedAttributes());

                return new TicketsResource($ticket);
            }
            return $this->notAuthorized($request->user()->name . ', you are not authorized to replace that resource');

    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $author, Tickets $ticket)
    {

            if ($this->isAble('delete', $ticket)) {
                $ticket->delete();
                return $this->ok('Ticket Successfully deleted.');
            }

            return $this->notAuthorized('You are not authorized to delete that resource.');
    }
}
