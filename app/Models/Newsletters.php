<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Newsletters extends Model
{
    use HasFactory;

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'date',
        'company_id',
        'archived',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }


    public function getOldNls() 
    {  
        $old = self::with('company')
                    ->where('date', '<', date('Y-m-d', strtotime("-30 days")))
                    ->where('archived', 0)
                    ->get();        
        

        if ($old) {    
            //$updated = 2;         
            $updated = self::where('date', '<', date('Y-m-d', strtotime("-30 days")))
                    ->where('archived', 0)
                    ->update(['archived' => 1]);
                    
            //dd($old, $updated);
        } 
        
        echo 'Procesed at:'. date("Y-m-d H:i:s") . ' \n '.$old.'\n\r updated records:'.$updated;
        return $old; 
    }
/*
    public function saveNl($data)
    {
        if ($this->findNl($data)) {
            $nl = self::create(array('name' => 'John'));
        }

        return;
    }

    public function findNl($data)
    {
        $result = self::where('company_id', $data['company_id'])
                    ->where('date', $data['date'])
                    //->toSql();
                    ->get();
        
        if ($result) { 
            return false;
        } 
        
        return true;        
    }
    */
}
