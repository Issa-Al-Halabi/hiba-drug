<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;


class CacheMiddleware
{
    private $storeTime;
    function __construct()
    {
        $this->storeTime = 30;
    }

    public function handle(Request $request, Closure $next)
    {
        if (Cache::has($this->cacheKey($request))) {
            return response(Cache::get($this->cacheKey($request)));
        }
        return $next($request);
    }

    public function terminate($request, $response)
    {

        if (Cache::has($this->cacheKey($request)))
            return;
        else
            Cache::put($this->cacheKey($request), $response->getContent(), $this->storeTime);
    }

    private function cacheKey($request)
    {
        return md5($request->fullUrl());
    }

}
