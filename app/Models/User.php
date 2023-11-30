<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

	public function isAdmin(): bool
    {
  		foreach($this->roles as $role)
  		{
  			if($role->name == 'Admin' ) return true;
  		}
		  return false;
    }

    public function isTrader(): bool
    {
  		foreach($this->roles as $role)
  		{
  			if($role->name == 'Trader' ) return true;
  		}
  		return false;
    }

    public function isOps(): bool
    {
  		foreach($this->roles as $role)
  		{
  			if($role->name == 'Ops' ) return true;
  		}
  		return false;
    }

	  public function isBackoffice(): bool
    {
  		foreach($this->roles as $role)
  		{
  			if($role->name == 'Backoffice' ) return true;
  		}
  		return false;
    }

	  public function isDealer(): bool
    {
  		foreach($this->roles as $role)
  		{
  			if($role->name == 'Dealer' ) return true;
  		}
  		return false;
    }

	  public function isAccounts(): bool
    {
  		foreach($this->roles as $role)
  		{
  			if($role->name == 'Accounts' ) return true;
  		}
  		return false;
    }
}
