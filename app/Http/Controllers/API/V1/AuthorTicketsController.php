<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Filters\V1\TicketFilter;
use App\Http\Requests\API\V1\StoreTicketsRequest;
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
        $model = [
            'title' => $request->input('data.attributes.title'),
            'description' => $request->input('data.attributes.description'),
            'status' => $request->input('data.attributes.status'),
            'users_id' => $author_id
        ];

        return new TicketsResource(Tickets::create($model));

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
