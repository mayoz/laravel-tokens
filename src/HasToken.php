<?php

namespace Mayoz\Token;

use Illuminate\Support\Carbon;

trait HasToken
{
    /**
     * Get the tokens for the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tokens()
    {
        return $this->hasMany(config('laravel-tokens.token'));
    }

    /**
     * Generate a new token and returns it.
     *
     * @param  int  $minute
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function generateToken($minute = null)
    {
        $expired_at = ((int) $minute > 0)
            ? Carbon::now()->addMinutes($minute)
            : null;

        return $this->tokens()->create([
            'api_token' => Generator::generate(),
            'expired_at' => $expired_at,
        ]);
    }
}
