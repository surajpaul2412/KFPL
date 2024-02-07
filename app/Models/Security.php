<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Security extends Model
{
    use HasFactory;

    protected $fillable = [
        'amc_id',
        'name',
        'symbol',
        'isin',
        'basket_size',
        'markup_percentage',
        'price',
        'cash_component',
        'status',
    ];

    public function amc()
    {
        return $this->belongsTo(Amc::class);
    }
}
