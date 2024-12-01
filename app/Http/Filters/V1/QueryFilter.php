<?php

namespace App\Http\Filters\V1;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

abstract class QueryFilter {
    protected $builder;
    protected $request;

    protected $sortable = [];

    public function __construct(Request $request){
        $this->request = $request;
    }

    protected function filter($arr) {
        foreach ($arr as $key => $value) {
            if(method_exists($this, $key)) {
                $this->$key($value);
            }
        }

        return $this->builder;
    }

    public function apply(Builder $builder) {
        $this->builder = $builder;


        foreach ($this->request->all() as $key => $value) {
            if(method_exists($this, $key)) {
                $this->$key($value);
            }
        }

        return $builder;
    }

    protected function sort($value) {
        $sortAttributes = explode(',', $value); // separate array

        foreach ($sortAttributes as $sortAttribute) {
            $direction = 'asc';

            if(strpos($sortAttribute, '-') === 0) { //check if '-' is the first letter of an element($sortAttribute) of the array($sortAttributes)
                $direction = 'desc'; //change sort direction if true
                $sortAttribute = substr($sortAttribute, 1); //remove '-' from the array
            }

            if(!in_array($sortAttribute, $this->sortable) && !key_exists($sortAttribute, $this->sortable)) {
                // if the sortAttribute(element) is not in this instance of allowed sort parameters && the element doesn't exist as a key in thde array
                continue; //then ignore
            }// else add it to the query

            $columnName = $this->sortable[$sortAttribute] ?? null; // if it's a key like 'createdAt' then use the value of the key, else return null

            if ($columnName == null) {
                $columnName = $sortAttribute;
            }

            $this->builder->orderBy($columnName, $direction);
        }
        // test with http://127.0.0.1:8000/api/v1/tickets?sort=-title
        // test with http://127.0.0.1:8000/api/v1/tickets?sort=status,-title
        // test with http://127.0.0.1:8000/api/v1/tickets?sort=-createdAt
        // test with http://127.0.0.1:8000/api/v1/tickets?sort=-createdAt,-title
    }
}
