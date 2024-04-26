<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Account extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'username',
        'password',
        'user_id',
        'provider',
        'provider_id'
    ];

    protected $hidden = [
        'user_id',
        'password',
        'provider_id'
    ];

    protected $casts = [
        'password' => 'hashed',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp'
    ];

    protected $dateFormat = 'U';

    public function user(): belongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function scopeWhereProvider(Builder $query, $provider) {
        $query->where('username', $provider['username'])->where('provider', $provider['provider'])->where('provider_id', $provider['provider_id']);
    }
}
