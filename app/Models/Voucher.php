<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Voucher extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'code', 'used_at'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
