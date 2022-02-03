<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = ["customer_id", "xero_id", "date_created","email","first_name","last_name","role","username","billing","shipping","email","avatar_url"];

    protected $casts = [
        'line_items' => 'array',
        'billing' => 'array',
        'shipping' => 'array',
        'date_created' => 'datetime:Y-m-d H:i:s',
    ];

    // public function setDateCreatedAttribute($value){

    // }

    public function orders(){
        return $this->hasMany(Order::class, "customer_id", "customer_id");
    }
}


