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
        'basket_size',
        'rate',
        'security_price',
        'markup_percentage',
        'actual_total_amt',
        'nav',
        'refund',
        'expected_refund',
        'deal_ticket',
        'utr_no',
        'screenshot',
        'remark','dispute',
        'dispute_comment'
    ];

    protected $appends = ['total_amt'];

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

    public function userroles()
    {
        return $this->hasMany(Roleuser::class, 'user_id', 'user_id');
    }

    // Define the relationship with Status
    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function getTotalAmtAttribute()
    {
        return totalTicketAmt($this);
    }

    /*
    public function getUserRolesAttribute()
    {
       $roles = $this->user->roles;
       $str = "";
       foreach($roles as $r)
       {
         $str .= $r->id . ",";
       }
       if($str !='')
       {
          $str = rtrim($str, ",");
       }
       return $str;
    }
    */
}
