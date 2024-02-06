<?php

declare(strict_types=1);

namespace Manychois\Butsing\Core;

/**
 * Represents a session.
 */
interface SessionInterface
{
    /**
     * Returns session data by key.
     *
     * @param string $key The key of the session data.
     *
     * @return null|int The session data, or null if not found.
     */
    public function getInt(string $key): ?int;

    /**
     * Returns session data by key.
     *
     * @param string $key The key of the session data.
     *
     * @return null|string The session data, or null if not found.
     */
    public function getString(string $key): ?string;

    /**
     * Removes session data by key.
     *
     * @param string $key The key of the session data.
     */
    public function remove(string $key): void;

    /**
     * Sets session data by key.
     *
     * @param string $key   The key of the session data.
     * @param int    $value The value of the session data.
     */
    public function setInt(string $key, int $value): void;

    /**
     * Sets session data by key.
     *
     * @param string $key   The key of the session data.
     * @param string $value The value of the session data.
     */
    public function setString(string $key, string $value): void;
}
