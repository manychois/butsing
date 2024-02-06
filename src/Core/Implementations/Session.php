<?php

declare(strict_types=1);

namespace Manychois\Butsing\Core\Implementations;

use Manychois\Butsing\Core\SessionInterface;
use RuntimeException;
use Stringable;

/**
 * Internal implementation of SessionInterface.
 */
class Session implements SessionInterface
{
    #region implements SessionInterface

    /**
     * @inheritDoc
     */
    public function getInt(string $key): ?int
    {
        $this->start();
        $value = $_SESSION[$key] ?? null;
        if ($value === null || \is_int($value)) {
            return $value;
        }

        throw new RuntimeException('Session data is not an integer.');
    }

    /**
     * @inheritDoc
     */
    public function getString(string $key): ?string
    {
        $this->start();
        $value = $_SESSION[$key] ?? null;
        if ($value === null || \is_string($value)) {
            return $value;
        }

        if ($value instanceof Stringable) {
            return $value->__toString();
        }

        throw new RuntimeException('Session data is not a string.');
    }

    /**
     * @inheritDoc
     */
    public function remove(string $key): void
    {
        $this->start();
        unset($_SESSION[$key]);
    }

    /**
     * @inheritDoc
     */
    public function setInt(string $key, int $value): void
    {
        $this->start();
        $_SESSION[$key] = $value;
    }

    /**
     * @inheritDoc
     */
    public function setString(string $key, string $value): void
    {
        $this->start();
        $_SESSION[$key] = $value;
    }

    #endregion implements SessionInterface

    /**
     * Starts the session if it is not already started.
     */
    private function start(): void
    {
        $status = \session_status();
        if ($status === \PHP_SESSION_DISABLED) {
            throw new RuntimeException('Session is disabled.');
        }
        if ($status === \PHP_SESSION_ACTIVE) {
            return;
        }
        $success = \session_start([
            'use_strict_mode' => true,
            'use_cookies' => true,
            'use_only_cookies' => true,
            'cookie_lifetime' => 0,
            'cookie_secure' => true,
            'cookie_httponly' => true,
            'cookie_samesite' => 'Strict',
            'use_trans_sid' => false,
            'lazy_write' => true,
        ]);
        if (!$success) {
            throw new RuntimeException('Failed to start session.');
        }
    }
}
