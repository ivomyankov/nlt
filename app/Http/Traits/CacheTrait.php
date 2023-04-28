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
            $old_cache = Cache::get('config');
            //echo "<p>Old cache: <pre>" . var_dump($cache). "</pre></p>";
            $new_cache = array_merge($old_cache, $config, ["last updated" => date("h:i:s.u")]);
            Cache::put('config', $new_cache, $seconds = 600);
            //$cache = Cache::get('config');
            //dump($new_cache);
            //echo "<p>Updated cache: <pre>" . var_dump($cache). "</pre></p>";
            return;
        } else {
            Cache::put('config', $config, $seconds = 600);
            $cache = Cache::get('config');
            //dump($cache);
            //echo "<p>New cache: <pre>" . var_dump($cache). "</pre></p>";
            return;
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
                //'mediaservices' => 'https://nlt.mediaservices.biz/storage/newsletters/',
                'internal_path' => 'storage/newsletters/'
            ];
            
            $this->addToCache($cache);
            return;
        } else {
            dd('No such cache');
        }
    }
    

}