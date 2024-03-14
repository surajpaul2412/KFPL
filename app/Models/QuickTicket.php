<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuickTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'security_id',
        'type',
        'payment_type',
        'basket_no',
        'basket_size',
        'actual_total_amt',
        'nav',
        'trader_id',
    ];

    public function security()
    {
        return $this->belongsTo(Security::class);
    }

    public function trader()
    {
        return $this->belongsTo(User::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
