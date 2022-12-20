<?php

namespace App\Services;

use App\Http\Controllers\ZipController;
use App\Http\Traits\CacheTrait;
use Exception;
use Illuminate\Support\Facades\File;
//use Intervention\Image\ImageManagerStatic as Image;


class SourceService
{
    use CacheTrait;

    private $storageDestinationPath;    

    private function setStorageDestinationPath($folder){
        $this->storageDestinationPath = storage_path("app/public/newsletters/" . $folder . '/');

        $storagePath = storage_path("app/public/newsletters/");
        $storage = [
            'storage_path'  => $storagePath,
            'folder'        => $folder
        ];
        
        $this->addToCache($storage);
        
    }
    
    public function handle($source, $folder, $dirControllerInstance) { 
        //dd($source['content']);
        
        $this->setStorageDestinationPath($folder);
        //dd($this->storageDestinationPath);

        if($source['source'] == 'url') {
            $source['type'] = 'html';
        } else if($source['content']->getClientMimeType() == 'text/html') {
            $source['type'] = 'html';
        } else if($source['content']->getClientMimeType() == 'application/zip') {
            $source['type'] = 'arhive';
            $unziped = new ZipController(); 
            $unziped->extractUploadedZip($source['content'], $this->storageDestinationPath, $dirControllerInstance);
        } else {
            $source['type'] = $source['content']->getClientMimeType();
        }

        //dd($source);

        if($source['type'] == 'html'){
            $htmlContent = file_get_contents($source['content']);
            //$htmlContent = $this->htmlHandler($source['content']);
            return $htmlContent;
        }
        
        return view('uploaded', ['newsletter' => 'Victoria']);   
        //dd('source hadler');       
    }


/*

    private function htmlHandler($raw) {
        $i=0;

        $this->saveImageLogoMXP();

        $content = file_get_contents($raw);
        
        return $content;
    }
*/
}