<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'expert_id',
        'category_id',
        'service_name',
        'service_description',
        'price',
        'photo',
    ];

    public function category(){
        return $this->belongsTo(Category::class,'category_id','id');
    }

    public function expert(){
        return $this->belongsTo(User::class,'expert_id','id');
    }

    public function book_service(){
        return $this->hasMany(BookService::class,'service_id','id');
    }

}
