<?php

namespace App\Services;

use Illuminate\Support\Facades\File;

class DirService
{
    public function dirCreator($folder) {
        //dd($folder);
        $storagePath = storage_path("app/public/newsletters/" . $folder);

        if (!File::exists( $storagePath)) {
            File::makeDirectory($storagePath, 0777, true); //755s
            if (!File::exists( $storagePath)) {
                dd($folder . ' can not be created');
            }
        }

        return $folder;
    }

    public function dirDelete($folder) {
        //dd($folder);
        $storagePath = storage_path("app/public/newsletters/" . $folder);
        if (File::exists( $storagePath)) {
            File::deleteDirectory($storagePath);
        }

        return redirect()->route('delete');
    }

    public function getFolders() {
        $folders = array();
        $storagePath = storage_path("app/public/newsletters/");
        $directories = File::directories($storagePath);
        
        foreach($directories as $dir) {
            array_push($folders, basename($dir));
        }

        //dd($folders);
        return $folders;
    }

    public function delete() {
        $folders = $this->getFolders();

        return view("delete",compact('folders'));
    }

    public function newsletters() {
        $folders = $this->getFolders();

        return view("newsletters",compact('folders'));
    }

    public function renameDirContentFolder($folder) {
        $directory = File::directories($folder);
        //dd($directory);
        if (is_dir($directory[0])) {
            rename($directory[0], str_replace(basename($directory[0]), 'img', $directory[0]));
            dd(basename($directory[0]) . ' is a direcotry');
        }
    }
}