<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Auth;

class RoleChecker
{

public function handle($request, Closure $next)
{
    $roles = Auth::check() ? Auth::user()->roles()->pluck('name')->toArray() : [];
    // dd($roles);
    
    if (in_array("admin", $roles)) {

        return $next($request);
    }

    abort(403, "Cannot access to this page");
}
}
