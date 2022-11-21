<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use ZipArchive;

class ZipController extends Controller
{
    function zipUploadForm(Request $request){
         
        return view("unzip");
    }

    function extractUploadedZip($archive, $storageDestinationPath, $dirControllerInstance){
        //dd($archive, $storageDestinationPath);
        $zip = new ZipArchive();
        $status = $zip->open($archive->getRealPath());
        if ($status !== true) {
         throw new \Exception($status);
        }
        else{
            //$storageDestinationPath= storage_path("app/public/newsletters/");
       
            if (!File::exists( $storageDestinationPath)) {
                File::makeDirectory($storageDestinationPath, 0777, true);
            }
            $zip->extractTo($storageDestinationPath);
            $zip->close();

            $dirControllerInstance->renameDirContentFolder($storageDestinationPath);
            return back()
             ->with('success','You have successfully extracted zip.');
        }
    }
}
