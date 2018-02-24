<?php

return [

    /*
    |--------------------------------------------------------------------------
    | User Model
    |--------------------------------------------------------------------------
    |
    | The fully qualified User model namespace. This model must have been
    | implemented with Laravel's Authenticatable contract. You are free to
    | change according to what you need.
    |
    | See the Laravel documentations for more information the Auth.
    |
    */

    'user' => App\User::class,

    /*
    |--------------------------------------------------------------------------
    | Token Model
    |--------------------------------------------------------------------------
    |
    | The fully qualified Token model namespace. This model class that provides
    | token service and will be associated with the User model.
    |
    | You can extend the base Token class.
    |
    */

    'token' => Mayoz\Token\Token::class,

];
