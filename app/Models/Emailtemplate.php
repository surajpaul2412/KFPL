<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Amc;

class Emailtemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject',
        'name',
        'template',
		'status'
    ];

}
