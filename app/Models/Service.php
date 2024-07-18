<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'service_name',
        'service_description',
        'photo',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function expert()
    {
        return $this->hasMany(ExpertInfos::class,);
    }

    public function book_service()
    {
        return $this->hasMany(BookService::class, 'service_id');
    }
}
