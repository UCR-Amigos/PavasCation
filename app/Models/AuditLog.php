<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_email',
        'method',
        'route',
        'url',
        'action',
        'ip_address',
        'user_agent',
        'payload',
        'model_type',
        'model_id',
        'event',
        'changes_before',
        'changes_after',
    ];

    protected $casts = [
        'payload' => 'array',
        'changes_before' => 'array',
        'changes_after' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
