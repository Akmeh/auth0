<?php
declare(strict_types=1);

namespace Akmeh;

use Auth0\SDK\Helpers\Cache\CacheHandler;
use Illuminate\Support\Facades\Cache;



/**
 * Cache wrapper class to support CacheHandler Interface required by Auth0\SDK\JWTVerifier
 */
class Auth0Cache implements CacheHandler
{

    /**
     * @param string $key
     * @param $val
     * @return void
     */
    public function set($key, $val)
    {
        return Cache::store(env('CACHE_DRIVER', 'redis'))->put($key, $val);
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function get($key)
    {
        return Cache::store(env('CACHE_DRIVER', 'redis'))->get($key);
    }

    /**
     * @param string $key
     * @return bool
     */
    public function delete($key)
    {
        return Cache::store(env('CACHE_DRIVER', 'redis'))->forget($key);
    }
}
