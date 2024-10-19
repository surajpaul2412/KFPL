<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Emailtemplate;

class Amc extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'expense_percentage', 'pdf_id', 'status', 'amc_pdf'];

    public function pdf()
    {
        return $this->belongsTo(Pdf::class);
    }

    public function securities()
    {
        return $this->hasMany(Security::class);
    }
	
	public function emailtemplates()
    {
        return $this->hasMany(Emailtemplate::class);
    }
}
