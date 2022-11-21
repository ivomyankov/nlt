<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UrlController extends Controller
{
    public function url($url) {
       
        //$storageDestinationPath= storage_path("app/public/nls/tarox_2022_10_20");
        
        //$homepage = file_get_contents($url);
        //dd($homepage);

function url_get_contents($url, $useragent='cURL', $headers=false, $follow_redirects=true, $debug=false) {

    // initialise the CURL library
    $ch = curl_init();

    // specify the URL to be retrieved
    curl_setopt($ch, CURLOPT_URL,$url);

    // we want to get the contents of the URL and store it in a variable
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);

    // specify the useragent: this is a required courtesy to site owners
    curl_setopt($ch, CURLOPT_USERAGENT, $useragent);

    // ignore SSL errors
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    // return headers as requested
    if ($headers==true){
        curl_setopt($ch, CURLOPT_HEADER,1);
    }

    // only return headers
    if ($headers=='headers only') {
        curl_setopt($ch, CURLOPT_NOBODY ,1);
    }

    // follow redirects - note this is disabled by default in most PHP installs from 4.4.4 up
    if ($follow_redirects==true) {
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); 
    }

    // if debugging, return an array with CURL's debug info and the URL contents
    if ($debug==true) {
        $result['contents']=curl_exec($ch);
        $result['info']=curl_getinfo($ch);
    }

    // otherwise just return the contents as a variable
    else $result=curl_exec($ch);

    // free resources
    curl_close($ch);
dd($result);
    // send back the data
    return $result;
}

url_get_contents($url);
    }
}
