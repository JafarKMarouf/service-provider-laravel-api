<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookService extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'expert_id',
        'service_id',
        'description',
        'delivery_time',
        'delivery_date',
        'location',
        'status',
    ];

    public function customer()
    {
        return $this->belongsTo(CustomerInfos::class, 'customer_id');
    }

    public function expert()
    {
        return $this->belongsTo(ExpertInfos::class, 'expert_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
}
