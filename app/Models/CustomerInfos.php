<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerInfos extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'country',
        'city',
        'mobile',
        'photo',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function bookService()
    {
        return $this->hasMany(BookService::class);
    }
}
