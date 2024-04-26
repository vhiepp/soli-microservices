<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable, HasUuids, Sluggable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'fullname',
        'uid',
        'firstname',
        'lastname',
        'date_of_birth',
        'gender',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'created_at',
        'updated_at',
        'role'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_of_birth' => 'timestamp',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp'
    ];

    protected $dates = [
        'date_of_birth',
    ];

    protected $dateFormat = 'U';

    protected $attributes = [
        'gender' => 'other',
        'role' => 'user'
    ];

    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class, 'user_id', 'id');
    }

    public function emails(): HasMany
    {
        return $this->hasMany(Email::class, 'user_id', 'id');
    }

    /**
     * This section manages outgoing friend requests initiated
     * by the current user. These requests are invitations sent
     * to other users, expressing a desire to connect and become
     * friends. The status of these requests may be pending until
     * accepted or rejected by the recipient.
     */
    public function sendFriendRequests(): BelongsToMany
    {
        return $this->belongsToMany(User::class, FriendRequest::class, 'user_request_id', 'user_is_requested_id')->withPivotValue(['status' => 'await']);
    }

    /**
     * This section retrieves and manages incoming friend requests
     * for the current user. Friend requests are invitations sent
     * by other users who wish to connect with the logged-in user.
     * These requests may be accepted or rejected.
     */
    public function friendRequests(): BelongsToMany
    {
        return $this->belongsToMany(User::class, FriendRequest::class, 'user_is_requested_id', 'user_request_id')->withPivotValue(['status' => 'await']);
    }

    /**
     * Retrieves the list of friends for the current user.
     * This function fetches the users who have a confirmed
     * friendship status with the currently logged-in user.
     * It retrieves and returns a collection or an array of
     * user objects representing the friends.
     */
    public function friends(): BelongsToMany
    {
        return $this->belongsToMany(User::class, UserFriend::class, 'user_one_id', 'user_two_id', 'id', 'id')->withPivotValue(['status' => 'friend']);
    }

    /**
     * Add another user as a friend for the current user.
     *
     * @param string $id The ID of the user to be added as a friend, or User $user The user to be added as a friend
     * @return bool Returns true if the friend was added successfully, otherwise returns false if they are already friends
     */
    public function addFriend(string|User $user = null): bool
    {
        if (gettype($user) == 'string') {
            $user = User::find($user);
        }
        if ($user && ($this->id != $user->id)) {
            $this->friends()->syncWithoutDetaching([$user->id => ['status' => 'friend']]);
            $user->friends()->syncWithoutDetaching([$this->id => ['status' => 'friend']]);
            return true;
        }
        return false;
    }

    public function following(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_follows', 'user_is_followed_id', 'user_follower_id', 'id', 'id')->withPivotValue(['status' => 'followed']);
    }

    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_follows', 'user_follower_id', 'user_is_followed_id', 'id', 'id')->withPivotValue(['status' => 'followed']);
    }

    public function avatars(): HasMany
    {
        return $this->hasMany(Avatar::class, 'user_id', 'id');
    }

    public function currentAvatar(): HasOne
    {
        return $this->hasOne(Avatar::class, 'user_id', 'id')->where('current', true);
    }

    public function getSlugNameAttribute(): string
    {
        $slugName = str($this->firstname)->slug(' ');
        $name = Str::wordCount($slugName) > 1 ? $slugName : str($this->fullname)->slug(' ');
        $wordCountRand = rand(2, 3);
        $name = Str::wordCount($name) > $wordCountRand ? implode(' ', array_slice(explode(' ', $name), 0, $wordCountRand)) : $name;
        return ' ' . str($name)->slug('');
    }
    
    public function sluggable(): array
    {
        return [
            'uid' => [
                'source' => 'slugName',
                'separator' => '',
                'method' => static function(string $string, string $separator): string {
                    $slug = strtolower(preg_replace('/[^a-z]+/i', $separator, $string));
                    if (strlen($slug) <= 8) {
                        if (rand(0, 1) > 0) {
                            $slug = '_' . $slug;
                        } else {
                            $slug = $slug . '_';
                        }
                    }
                    return $slug;
                },
            ]
        ];
    }


    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
