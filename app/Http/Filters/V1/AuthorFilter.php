<?php

namespace App\Http\Filters\V1;

class AuthorFilter extends QueryFilter {
    protected $sortable = [
        'id',
        'name',
        'email',
        'createdAt' => 'created_at',
        'updatedAt' => 'updated_at',
    ];

    // test with http://127.0.0.1:8000/api/v1/authors?sort=-name
    
    public function include($value) {
        return $this->builder->with($value);
    }

    public function id($value) {
        return $this->builder->whereIn('id',explode(',',$value));
    }
    //url to test http://127.0.0.1:8000/api/v1/tickets?filter[status]=Completed

    public function email($value) {
        $likeStr = str_replace('*','%',$value);

        return $this->builder->where('email','ilike',$likeStr);
    }

    public function name($value) {
        $likeStr = str_replace('*','%',$value);

        return $this->builder->where('name','ilike',$likeStr);
    }


    public function createdAt($value) {
        $dates = explode(',',$value);
        //dd($dates);
        //if date is a range/more than 1
        if(count($dates) > 1) {
            return $this->builder->whereBetween('created_at',$dates);
        }

        return $this->builder->whereDate('created_at', $dates);
    }

    public function updatedAt($value) {
        $dates = explode(',',$value);

        //if date is a range/more than 1
        if(count($dates) > 1) {
            return $this->builder->whereBetween('updated_at',$dates);
        }

        return $this->builder->whereDate('updated_at', $dates);
        //url to test http://127.0.0.1:8000/api/v1/tickets?filter[createdAt]=2024-11-28,2024-11-29
    }
}
