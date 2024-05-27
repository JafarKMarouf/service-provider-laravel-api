<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_service_id',
        'payment_expert_id',
        'amount',
        'operation_number',
    ];

    public function bookservice(): BelongsTo
    {
        return $this->belongsTo(BookService::class, 'book_service_id', 'id');
    }

    public function expert(): BelongsTo
    {
        return $this->belongsTo(User::class, 'payment_expert_id', 'id');
    }
}
