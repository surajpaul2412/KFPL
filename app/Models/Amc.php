<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Amc extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'pdf', 'status'];

    public function securities()
    {
        return $this->hasMany(Security::class);
    }
}
