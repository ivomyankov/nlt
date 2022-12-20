<?php

namespace App\Http\Livewire;

use Livewire\Component;

class Newsletters extends Component
{
    public $nls;
    public $weeks = array();
    public $directories;
    public $convertion = [
        1 => 'Mon',
        2 => 'Tue',
        3 => 'Wed',
        4 => 'Thu',
        5 => 'Fri',
        6 => 'Sat',
        7 => 'Sun',
    ];

    public function render()
    {
        //dd($this->nls);
        $this->weeks = $this->getDataParams();

        return view('livewire.newsletters');
    }

    public function getDataParams() {
        foreach ($this->nls as $key => $nl) { //dd($nl->company->name);
            $date = $nl->date;
            $company = $nl->company->name;
            $week_day = date("D", strtotime($date));
            $week_number = date("W", strtotime($date));

            if (!isset($weeks[$week_number][$week_day])) {
                $weeks[$week_number][$week_day] = [];
            }
            
            array_push($weeks[$week_number][$week_day], ['nl' => $nl]);
        }

        $weeks = $this->addEmptyDays($weeks);

        //dd($weeks);
        return $weeks;
    }

    public function addEmptyDays($weeks) {
        //dump($weeks);
        foreach ($weeks as $week_number => $week) {             
            $temp = [];
            for ($i = 1; $i < 8; $i++) {
                if (!array_key_exists($this->convertion[$i], $week)) {
                    $temp[$this->convertion[$i]] = [];
                } else {
                    $temp[$this->convertion[$i]] = $week[$this->convertion[$i]];
                }
            }
            $weeks[$week_number] = $temp;
            //dd($temp);
        }
        
        //dd($weeks);
        return $weeks;
    }

    public function colByWeekDay($val) {
        if ( in_array($val, ["Mon", "Tue", "Wed", "Thu", "Fri"]) ) {
            $col = 'col-sm-2';
        } else {
            $col = 'col-sm-1';
        }

        //dump($col, $val);
        return $col;
    }

    public function inArray($val) {
        if ( in_array($val, ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"]) ) {
            return true;
        }

        return false;
    }
}
