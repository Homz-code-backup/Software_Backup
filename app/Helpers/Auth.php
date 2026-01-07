<?php

class Auth
{
    private static ?User $user = null;

    /**
     * Get the currently authenticated user.
     * @return User|null
     */
    public static function user(): ?User
    {
        if (self::$user !== null) {
            return self::$user;
        }

        if (isset($_SESSION['user_id'])) {
            self::$user = User::find($_SESSION['user_id']);
            return self::$user;
        }

        return null;
    }

    /**
     * Check if the user is authenticated.
     * @return bool
     */
    public static function check(): bool
    {
        return self::user() !== null;
    }

    /**
     * Get the ID of the currently authenticated user.
     * @return int|null
     */
    public static function id(): ?int
    {
        return $_SESSION['user_id'] ?? null;
    }
}
