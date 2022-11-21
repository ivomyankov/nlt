<?php
namespace App\Http\Traits;

use Illuminate\Support\Facades\Cache;

trait CacheTrait {

    public function addToCache($config)
    {
        if (Cache::has('config')) {
            if (!is_array($config)) {
                dd($config);
            }
            $cache = Cache::get('config');
            //echo "<p>Old cache: <pre>" . var_dump($cache). "</pre></p>";
            $new_cache = array_merge($cache, $config);
            Cache::put('config', $new_cache, $seconds = 600);
            $cache = Cache::get('config');
            //echo "<p>Updated cache: <pre>" . var_dump($cache). "</pre></p>";
        } else {
            Cache::put('config', $config, $seconds = 600);
            $cache = Cache::get('config');
            //echo "<p>New cache: <pre>" . var_dump($cache). "</pre></p>";
        }
        
    }

    public function clearCache() {
        Cache::flush();
        $this->buildCache('config');
        return;
    }

    public function getCache($var) {
        if (Cache::has($var)) {
            return Cache::get($var);
        } else {
            dd('No such cache');
        }
    }

    public function buildCache($var) {
        if (!Cache::has($var) && $var == 'config') {
            $cache = [
                'base_url'      => url('/'),
                'mediaservices' => 'https://newsletter.mediaservices.biz/zzz/',
                'internal_path' => 'storage/newsletters/'
            ];
            
            $this->addToCache($cache);
            return;
        } else {
            dd('No such cache');
        }
    }

}