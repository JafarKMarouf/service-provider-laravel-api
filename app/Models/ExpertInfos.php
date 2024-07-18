<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpertInfos extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'service_id',
        'mobile',
        'city',
        'country',
        'photo',
        'description',
        'rating',
        'working_hours',
        'price',
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
}