<?php

namespace App\Http\Controllers;

use Exception;
use App\Http\Traits\CacheTrait;
use App\Models\Newsletters;
use Illuminate\Http\Request;
use App\Services\HtmlService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

//use Illuminate\Support\Facades\View;

class HtmlController extends Controller
{
    use CacheTrait;

    private $html_service;

    public function __construct(HtmlService $html_service)
    {
        $this->html_service = $html_service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Newsletters $nls)
    {
        $nls = $nls::with('company')->get();

        return view("newsletters",compact('nls'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //dd('Save HTML file to disk');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id = 0)
    {
        $config = Cache::get('config');
        //dd($config, Storage::disk('local'), Storage::disk('nls'), $config['storage_path'] . $config['folder'] . '/index.html');
        try {
            $html = File::get($config['storage_path'] . $config['folder'] . '/index.html');
            return view('editor', ['html' => $html]);
        }
        catch(Exception $e) {
            dd($e->getMessage());
        }
        //if (Storage::disk('nls')->exists($config['folder'] . '/index.html')) {}

        dd($config);
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id = null) 
    {
        $config = Cache::get('config');
        
        $validated = $request->validate([
            'html' => 'required',
        ]);

        try {
            File::put($config['storage_path'] . $config['folder'] . '/index.html', $validated['html']);
            return view('uploaded', ['cache' => $config]);
        }
        catch(Exception $e) {
            dd($e->getMessage(), $config);
        }
        
        dd($validated['html']);
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Save to file.
     *
     * @param  string  $html_content
     */
    public function saveToFile($html_content)
    {   
        $config = Cache::get('config');
        //dd($storage['storage_path'] . $storage['folder']);

        try {
            File::put($config['storage_path'] . $config['folder'] . '/index.html', $html_content);
            return;
        }
        catch(Exception $e) {
            dd($e->getMessage());
        }

        dd($config['storage_path'] . $config['folder'] . '/index.html');
    }

    private function saveToDb()
    {
        $config = Cache::get('config');
        //dd($config);
        $data = [
            "date" => $config['date'],
            "company_id" => $config['company_id']
        ];

        Newsletters::firstOrCreate( $data); 
        
        return;
    }

    /**
     * Handles html creation and manupulation
     *
     * @param  string  $html_content
     * @return \Illuminate\Http\Response
     */
    public function handleCreation($html_content)
    {   
        /*     
        $old = new ZipController;
        $old->arhivateNewsletter();
        dd($old);
        
        */
        try {
            $this->html_service->detectLanguage($html_content);
            $html_content = $this->html_service->checkHtmlStructure($html_content);
            $html_content = $this->html_service->replaceAsciiInLinks($html_content);
            $html_content = $this->html_service->fixCommonIssues($html_content);  
            //$html_content = $this->html_service->escapeSpecialCharacters($html_content);            
            $html_content = $this->html_service->imagesHandler($html_content);
            $html_content = $this->html_service->addHeader($html_content);   
            $html_content = $this->html_service->addFooter($html_content);       
        }
        catch(Exception $e) {
            dd($e->getMessage());
        }
                       
        $this->saveToFile($html_content);
        $this->saveToDb();
    }
}
