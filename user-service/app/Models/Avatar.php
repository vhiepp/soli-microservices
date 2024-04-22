<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Avatar extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'url',
        'user_id',
        'current'
    ];

    protected $hidden = [
        'user_id',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp'
    ];

    protected $dateFormat = 'U';

    protected $attributes = [
        'url' => '/images/avatars/avatar-default.png',
        'current' => true,
    ];

    public static function boot(): void
    {
        parent::boot();

        static::retrieved(function ($avatar) {
            if (!str(str_replace(' ', '', $avatar->url))->isUrl()) {
                if ($avatar->url[0] != '/') $avatar->url = '/' . $avatar->url;
                $avatar->url = env('APP_URL') . $avatar->url;
            }
        });
    }

    public function user():BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
