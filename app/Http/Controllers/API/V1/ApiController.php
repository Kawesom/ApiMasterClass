<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponses;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ApiController extends Controller
{
    use ApiResponses, AuthorizesRequests;

    protected $policyClass;

    public function include(string $relationship): bool {
        $param = request()->get('include');

        if (!isset($param)) {
            return false;
        }

        $includeValues = explode(',',strtolower($param));

        return in_array(strtolower($relationship), $includeValues);
    }

    /**
     *  for picking version of policy
     * @param mixed $ability
     * @param mixed $targetModel
     * @return \Illuminate\Auth\Access\Response
     */
    public function isAble($ability, $targetModel)
    {
        $gate = Gate::policy($targetModel::class, $this->policyClass);

        try {
            $gate->authorize($ability, [$targetModel]);
            return true;
        } catch (AuthorizationException $e) {
            return false;
        }
    }

}
