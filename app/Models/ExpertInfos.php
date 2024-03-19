<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpertInfos extends Model
{
    use HasFactory;

    protected $fillable = [
        'expert_id',
        'mobile',
        'city',
        'country',
        'photo',
        'description',
        'certificate',
        'rating',
        'working_hours'
    ];
    public function expert(){
        return $this->belongsTo(User::class,'expert_id','id');
    }

    // public function where();
}
