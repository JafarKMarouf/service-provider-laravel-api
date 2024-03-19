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

    ];

    public function category(){
        return $this->belongsTo(Category::class,'category_id','id');
    }

    public function expert(){
        return $this->belongsTo(User::class,'expert_id','id');
    }
}
