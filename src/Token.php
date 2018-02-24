<?php

namespace Mayoz\Token;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tokens';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'api_token',
        'expired_at',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['expired_at'];

    /**
     * Set the expired timestamp value.
     *
     * @param  mixed  $value
     * @return void
     */
    public function setExpiredAtAttribute($value)
    {
        if (! is_null($value) && ! $value instanceof Carbon) {
            $value = Carbon::parse($value);
        }

        $this->attributes['expired_at'] = $value;
    }

    /**
     * Determine if the token is expired or not.
     *
     * @return bool
     */
    public function isExpired()
    {
        if (is_null($this->expired_at)) {
            return false;
        }

        return now()->greaterThan(new Carbon($this->expired_at));
    }

    /**
     * Determine if the token is not expired.
     *
     * @return bool
     */
    public function isNotExpired()
    {
        return ! $this->isExpired();
    }

    /**
     * Get the user that owns the token.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(config('laravel-tokens.user'));
    }
}
