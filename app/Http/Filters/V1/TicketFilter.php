<?php

namespace App\Http\Filters\V1;

class TicketFilter extends QueryFilter {

    public function include($value) {
        return $this->builder->with($value);
    }

    public function status($value) {
        return $this->builder->whereIn('status',explode(',',$value));
    }

    public function title($value) {
        $likeStr = str_replace('*','%',$value);

        return $this->builder->where('title','ilike',$likeStr);
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
    }
}
