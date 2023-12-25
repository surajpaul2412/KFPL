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
        'user_id',
        'status_id',
        'type',
        'payment_type',
        'basket_no',
        'rate',
        'total_amt',
        'utr_no',
        'screenshot',
        'remark'
    ];

    // Define the relationship with Security
    public function security()
    {
        return $this->belongsTo(Security::class);
    }

    // Define the relationship with User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Define the relationship with Status
    public function status()
    {
        return $this->belongsTo(Status::class);
    }
}
