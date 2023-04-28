<?php

namespace App\Http\Controllers;

use App\Http\Controllers\HtmlController;
use App\Http\Requests\nlsDetailsRequest;
use App\Services\DirService;
use App\Services\SourceService;
use App\Http\Traits\CacheTrait;
use App\Models\Company;
use Illuminate\Support\Str;
use DOMDocument;
//use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SourceController extends Controller
{
    use CacheTrait;

    private $htmlController;

    public function __construct(HtmlController $html_controller)
    {
        $this->clearCache(); 
        //dd($this->getCache('config'));
        $this->htmlController = $html_controller;
    }

    public function index(SourceService $source_service, nlsDetailsRequest $request, DirService $dir)
    {         
        $validated = $request->validated();
        //dd($validated);
        
        $this->addInCache($validated);
        $cache = $this->getCache('config');
        //dd($cache);
        
        $folder = $dir->dirCreator($cache['date'] . '_' . $cache['company']);
        //dd($folder);
        
        $source = $this->source($validated);

        // Source Handler takes care of whatever type submited (arhive, html, url)
        // then creates the neded folders on disk and saves images.
        // returns the content of the html
        $htmlContent = $source_service->handle($source, $folder, $dir);

        // saves the content of the html to html file on disk
        $this->htmlController->handleCreation($htmlContent);
        //$this->html_service->saveHtmlFile($this->htmlController, $htmlContent);
        //$this->saveHtmlFile($htmlContent);
      
        $cache = $this->getCache('config');
        
        //dd($cache);
        return view('uploaded', ['cache' => $cache]);
    }

    private function source($validated) {       
        $source = [
            'source' => array_key_first($validated),
            'content'   => $validated[array_key_first($validated)]
        ];
        
        return $source;
    }

    private function addInCache($validated)
    {
        //$company = Company::find($validated['company_id']);
        //dd($company['name']);
        $this->addToCache(['server' => $validated['server']]);
        $this->addToCache(['company' => $validated['company']]);
        $this->addToCache(['date' => $validated['date']]);
        //$this->addToCache(['company_id' => $validated['company_id']]);

        return;
    }

    public function deleteFolder($folder, DirService $dir) {
        if ($dir->dirDelete($folder)) {
            dd($folder . ' deleted');
        } else {
            dd($folder . ' NOT deleted');
        }
    }

    private function saveHtmlFile($htmlContent) {
        //dd($htmlContent);
        $this->htmlController->saveToFile($htmlContent);
    }
}
