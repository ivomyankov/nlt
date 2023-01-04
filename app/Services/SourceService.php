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
    
    public function handle($source, $folder, $dirControllerInstance):string { 
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
            // extracts files and 
            $unziped->extractUploadedZip($source['content'], $this->storageDestinationPath, $dirControllerInstance); 
            $file = $this->findHtml($this->storageDestinationPath);
            $htmlContent = file_get_contents($this->storageDestinationPath.$file);
            
            return $htmlContent;
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

    
    /**
     * Finds html file in that dir
     *
     * @param  string  $destPath
     * @return string
     */
    private function findHtml($destPath):string
    {
        $file = glob($destPath."*.{html, htm}", GLOB_BRACE);
        if ($file){
            return basename($file[0]);
        }
        
        dd('No html file');
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