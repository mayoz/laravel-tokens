<?php

namespace Mayoz\Token;

class Generator
{
    /**
     * The token generator function.
     *
     * @var callable
     */
    protected static $handler;

    /**
     * Get the callback function.
     *
     * @return callable
     */
    public static function handler()
    {
        if (is_null(static::$handler)) {
            static::$handler = function () {
                return str_random(36);
            };
        }

        return static::$handler;
    }

    /**
     * Register a custom generator handler.
     *
     * @param  callable  $handler
     * @return void
     */
    public static function extend(callable $handler)
    {
        static::$handler = $handler;
    }

    /**
     * Generate a new token value.
     *
     * @return string
     */
    public static function generate()
    {
        return value(static::handler());
    }
}
