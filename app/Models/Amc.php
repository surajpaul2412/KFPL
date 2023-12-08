<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Amc extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'pdf_id', 'status'];

    public function pdf()
    {
        return $this->belongsTo(Pdf::class);
    }

    public function securities()
    {
        return $this->hasMany(Security::class);
    }
}
