<?php

namespace App\Http\Livewire;

use Livewire\Component;

class Newsletters extends Component
{
    public $folders = array();
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
        $this->folders = $this->getDataParams();

        return view('livewire.newsletters');
    }

    public function getDataParams() {
        foreach ($this->directories as $key => $dir) {
            $date = substr($dir, 0, 10);
            $name = substr($dir, 11);
            $week_day = date("D", strtotime($date));
            $week_number = date("W", strtotime($date));

            if (!isset($folders[$week_number][$week_day])) {
                $folders[$week_number][$week_day] = [];
            }
            
            array_push($folders[$week_number][$week_day], ['directory' => $dir,'date' => $date, 'name' => $name]);
        }

        $folders = $this->addEmptyDays($folders);

        //dd($folders);
        return $folders;
    }

    public function addEmptyDays($folders) {
        //dump($folders);
        foreach ($folders as $week_number => $week) {             
            $temp = [];
            for ($i = 1; $i < 8; $i++) {
                if (!array_key_exists($this->convertion[$i], $week)) {
                    $temp[$this->convertion[$i]] = [];
                } else {
                    $temp[$this->convertion[$i]] = $week[$this->convertion[$i]];
                }
            }
            $folders[$week_number] = $temp;
            //dd($temp);
        }
        
        //dd($folders);
        return $folders;
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
