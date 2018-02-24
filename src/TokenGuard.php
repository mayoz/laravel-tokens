<?php

namespace Mayoz\Token;

use Illuminate\Http\Request;
use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;

class TokenGuard implements Guard
{
    use GuardHelpers;

    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * The currently authenticated token.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $token;

    /**
     * The name of the query string item from the request containing the API token.
     *
     * @var string
     */
    protected $inputKey;

    /**
     * The name of the token "column" in persistent storage.
     *
     * @var string
     */
    protected $storageKey;

    /**
     * Create a new token guard instance.
     *
     * @param  \Illuminate\Contracts\Auth\UserProvider  $provider
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function __construct(UserProvider $provider, Request $request)
    {
        $this->request = $request;
        $this->provider = $provider;
        $this->inputKey = 'api_token';
        $this->storageKey = 'api_token';
    }

    /**
     * Get the currently authenticated user.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function user()
    {
        if (is_null($this->user) && ($token = $this->token()) && $token->isNotExpired()) {
            $this->user = (clone $token)->user;
        }

        return $this->user;
    }

    /**
     * Get the currently token model.
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function token()
    {
        if (is_null($this->token)) {
            $this->token = $this->retrieveTokenForRequest(
                [$this->inputKey => $this->getTokenCredentials()]
            );
        }

        return $this->token;
    }

    /**
     * Get the token credentials for the current request.
     *
     * @return string
     */
    protected function getTokenCredentials()
    {
        $token = $this->request->get($this->inputKey);

        if (empty($token)) {
            $token = $this->request->bearerToken();
        }

        return $token;
    }

    /**
     * Retrieve the token for the current request.
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    protected function retrieveTokenForRequest(array $credentials)
    {
        if (array_key_exists($this->inputKey, $credentials)) {
            return $this->provider->retrieveByCredentials(
                [$this->storageKey => $credentials[$this->inputKey]]
            );
        }
    }

    /**
     * Validate a user's credentials.
     *
     * @param  array  $credentials
     * @return bool
     */
    public function validate(array $credentials = [])
    {
        if ($token = $this->retrieveTokenForRequest($credentials)) {
            return $token->isNotExpired();
        }

        return false;
    }

    /**
     * Set the current request instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return $this
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;

        return $this;
    }
}
