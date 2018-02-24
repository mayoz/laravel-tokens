# Laravel Multi Token

## Introduction

This package provides a simple solution for give multiple token to your application's users. This solution is similar to Laravel's TokenGuard class.

This package is for Laravel 5.5 and above.


## Installation

You can install the package via composer using:

```
composer require mayoz/laravel-tokens
```

The service provider will automatically get registered for Laravel 5.5 and above versions. If you want you can add the service provider in `config/app.php` file:

```php
'providers' => [
    // ...
    Mayoz\Token\TokenServiceProvider::class,
];
```

If you are going to make changes the default migration, you can publish the `migration` file with:

```
php artisan vendor:publish --provider="Mayoz\Token\TokenServiceProvider" --tag="laravel-tokens-migrations"
```

Then, you can create the `tokens` table by running the migrations:

```
php artisan migrate
```

You can publish the config file with:

```
php artisan vendor:publish --provider="Mayoz\Token\TokenServiceProvider" --tag="laravel-tokens-config"
```

If you need you are free to change your `config` file.


## Implementation

After installation, you can implement the new feature for your application.

Add the `Mayoz\Token\HasToken` trait to your `App\User` model. This trait will provide a few helper methods to your model which allow you to inspect the authenticated user's tokens:

```php
<?php

namespace App;

use Mayoz\Token\HasToken;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasToken, Notifiable;
    
    // ...
}
```

And finally, you will add the new guard to your application. Open the `config/auth.php` file and apply following changes:

```php
  'guards' => [
        // ...

        'api' => [
            'driver' => 'multi-token',
            'provider' => 'tokens',
        ],
    ],

    'providers' => [
        // ...

        'tokens' => [
            'driver' => 'eloquent',
            'model' => Mayoz\Token\Token::class,
        ],
    ],
```

Congratulations!


## Usage

When you need it (after login or any actions later), use the helper function to create a new token.

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TokenController
{
     public function __invoke(Request $request)
     {
         $token = $request->user()->generateToken();
         
         return $token;
     }
}
```

By default tokens never expire if you do not pass the lifetime when generation. For define expiration, you can pass the time period parameter (in minutes) to `generateToken` method.

Generate a new token of 10 minutes life with:

```php
$token = $request->user()->generateToken(10);
```

The token are not refreshed, token will die when expired. The authentication attempts with expired token will fail.

The authentication process is similar to that of the standard Laravel api_token flows:

The token guard is looking for the token:

1. Firstly looking the URL for parameter `?api_token=XXX`
2. If not exists token, looking the header for `Authorization: Bearer XXX`

Finally, if you need the current token model information underlying the authentication process, you can use the `token` method.


```
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController
{
     public function __construct()
     {
          $this->middleware('auth:api');
     }
     
     public function __invoke(Request $request)
     {
         return [
             'user' => $request->user(),
             'token' => $request->token(),
         ];
     }
}

```