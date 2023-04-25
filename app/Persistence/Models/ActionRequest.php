<?php

namespace App\Persistence\Models;

use App\Enums\ActionRequestEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActionRequest extends Model
{
    use HasFactory;

    protected $casts = ['action_data' => 'array'];

    protected $fillable = [
        'user_id',
        'action_data',
        'request_type'
    ];

    public function scopePending($query)
    {
        return $query->where('status', ActionRequestEnum::PENDING->value);
    }
}
