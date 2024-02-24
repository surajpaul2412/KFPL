<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Amc;
use App\Models\User;

class Roleuser extends Model
{
    use HasFactory;

    protected $fillable = [
        'role_id',
        'user_id'
    ];

    protected $table = 'role_user';

}
