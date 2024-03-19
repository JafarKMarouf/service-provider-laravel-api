<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerInfos extends Model
{
    use HasFactory;
    protected $fillable = [
        'customer_id',
        'country',
        'city',
        'mobile',
        'photo',
    ];

    public function customer(){
        return $this->belongsTo(User::class,'customer_id');
    }
}
