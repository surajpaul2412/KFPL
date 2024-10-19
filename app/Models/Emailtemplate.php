<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Amc;

class Emailtemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'amc_id',
        'type',
        'name',
        'template',
		'status'
    ];

    // Define the relationship with AMC
    public function amc()
    {
        return $this->belongsTo(Amc::class);
    }

}
