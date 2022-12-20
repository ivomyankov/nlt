<?php

namespace App\View\Components;

use App\Models\Company;
use Illuminate\View\Component;

class CompanyDropdown extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        $companies = $this->getCompanies();

        return view('components.company-dropdown')->with(['companies' => $companies]);
    }

    private function getCompanies()
    {
        $companies = Company::orderBy('name', 'desc')->get();

        return $companies;
    }
}
