<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Amc;
use App\Models\User;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'security_id',
        'employee_id',
        'stage',
        'type',
        'pay_mode',
        'no_basket',
        'total_share',
        'trade_value',
        'rate',
        'total_amt'
    ];

    public function security()
    {
        return $this->belongsTo(Security::class);
    }

    public function employee()
    {
        return $this->belongsTo(User::class);
    }
}
