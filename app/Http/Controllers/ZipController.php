<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Newsletters;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class ZipController extends Controller
{
    public function __invoke(Newsletters $dbNls)
    { dd('invoce');
        $old = $this->arhivateNewsletter($dbNls);
        
        return $old;
    }

    public function zipUploadForm(Request $request){
         
        return view("unzip");
    }

    public function extractUploadedZip($archive, $storageDestinationPath, $dirControllerInstance){
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

            //$dirControllerInstance->renameDirContentFolder($storageDestinationPath);
            return back()
             ->with('success','You have successfully extracted zip.');
        }
    }

    public function arhivateNewsletters(){
        
        $dbNls = new Newsletters();
        $old = $dbNls->getOldNls();
        //dd(Newsletters::find(1));
        //dd($old, $old[0]->company->name);
        if (!is_null($old)){   
            foreach ($old as $key => $nl) {
                //dd($nl->date.'_'.$nl->company->name);
                $dir = storage_path('app/public/newsletters/'.$nl->date.'_'.$nl->company->name);
                $files = File::files($dir);
                //dd($files, $nl->date.'_'.$nl->company->name);
                $zip = new \ZipArchive();        
                $zip->open(storage_path('app/public/newsletters/'.$nl->date.'_'.$nl->company->name).'/archive.zip', \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
                if (is_dir($dir)) {
                    foreach ($files as $key => $file){
                        $relativeName = basename($file);
                        //dd($file, $relativeName, storage_path('app/public/newsletters/'.$nl->date.'_'.$nl->company->name).'/archive.zip');
                        $zip->addFile($file, $relativeName);                
                //dump($file);
                    }
                }
                
                $zip->close();

                $nl->update(['archived' => 1]);

                File::delete($files);
            } 
            dd($old);
            
            //return response()->download($zip_file);
        } else {
            dd($old);
        }
        
        return $old;
    }

    public function arhivateNewsletter(Newsletters $nls, $id){
        $nl = $nls::find($id);
        //dd($nl);
        $dir = storage_path('app/public/newsletters/'.$nl->date.'_'.$nl->company);
        $files = File::files($dir);
        //dd($files, $nl->date.'_'.$nl->company->name);
        $zip = new \ZipArchive();        
        $zip->open($dir.'/archive.zip', \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        if (is_dir($dir)) {
            foreach ($files as $key => $file){
                $relativeName = basename($file);
                //dd($file, $relativeName, storage_path('app/public/newsletters/'.$nl->date.'_'.$nl->company->name).'/archive.zip');
                if ($relativeName != 'archive.zip') {
                    $zip->addFile($file, $relativeName); 
                }
                               
        //dump($file);
            }
        }
        
        $zip->close();

        $nl->update(['archived' => 1]);
        
        $headers = array(
            'Content-Type: application/pdf',
          );

        return response()->download($dir.'/archive.zip', $nl->date.'_'.$nl->company.'.zip', $headers);
    }

    public function unzip(Newsletters $nls, $id)
    {
        $nl = $nls::find($id);
        $storageNlPath = storage_path("app/public/newsletters/".$nl->date.'_'.$nl->company);
        //dd($nl, $storageNlPath);
        
        $zip = new ZipArchive();
        $status = $zip->open($storageNlPath . '/archive.zip');
        if ($status !== true) {
         throw new \Exception($status);
        }
        else{       
            if (!File::exists( $storageNlPath)) {
                File::makeDirectory($storageNlPath, 0755, true);
            }
            $zip->extractTo($storageNlPath);
            $zip->close();

            $nl->update(['archived' => 0]);

            return redirect('storage/newsletters/' . $nl->date.'_'.$nl->company . '/index.html');
        }
        
    }
}
