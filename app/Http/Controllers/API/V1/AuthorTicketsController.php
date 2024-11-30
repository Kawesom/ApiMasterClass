<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Filters\V1\TicketFilter;
use App\Http\Resources\V1\TicketsResource;
use App\Models\Tickets;
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
}
