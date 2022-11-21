<?php

namespace App\View\Components;

use Illuminate\Support\Facades\Cache;
use Illuminate\View\Component;
use Illuminate\Support\Str;

class Uploaded extends Component
{
    private $config;
    public $html_path;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->config = Cache::get('config');
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        //dd($this->config);
        //$this->html_path = $this->config['storage_path'] . $this->config['folder'] . '/index.html';

        $content = $this->getHtml();
        $content = $this->replaceImageUrl($content);

        return view('components.uploaded')->with(['html' => $content]);
    }

    private function getHtml() {
        $content = file_get_contents($this->config['storage_path'] . $this->config['folder'] . '/index.html');
        
        return $content;
    }

    private function replaceImageUrl($content) {
        $content = Str::replace($this->config['mediaservices'], $this->config['internal_path'], $content);
        echo $content;
        //return $content;
    }
}
