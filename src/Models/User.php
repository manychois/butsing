<?php

declare(strict_types=1);

namespace Manychois\Butsing\Models;

use DateTime;
use DateTimeZone;
use Manychois\Butsing\Persistence\UserDto;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * Represents a user of the Butsing system.
 */
class User
{
    private int $id = 0;
    private UuidInterface $uuid;
    private string $username = '';
    private string $email = '';
    private string $role = '';
    private string $status = '';
    private DateTime $createdAt;
    private DateTime $updatedAt;
    private string $newPassword = '';

    /**
     * Sets the properties of the user from the data transfer object.
     *
     * @param User    $user The user to be updated.
     * @param UserDto $dto  The data transfer object.
     */
    public static function setFromDto(User $user, UserDto $dto): void
    {
        if ($dto->id !== null) {
            $user->id = $dto->id;
        }
        if ($dto->uuid !== null) {
            $user->uuid = Uuid::fromBytes($dto->uuid);
        }
        if ($dto->username !== null) {
            $user->username = $dto->username;
        }
        if ($dto->email !== null) {
            $user->email = $dto->email;
        }
        if ($dto->role !== null) {
            $user->role = $dto->role;
        }
        if ($dto->status !== null) {
            $user->status = $dto->status;
        }
        // phpcs:ignore Zend.NamingConventions.ValidVariableName.NotCamelCaps
        if ($dto->created_at !== null) {
            // phpcs:ignore Zend.NamingConventions.ValidVariableName.NotCamelCaps
            $user->createdAt = new DateTime($dto->created_at, new DateTimeZone('UTC'));
        }
        // phpcs:ignore Zend.NamingConventions.ValidVariableName.NotCamelCaps
        if ($dto->updated_at !== null) {
            // phpcs:ignore Zend.NamingConventions.ValidVariableName.NotCamelCaps
            $user->updatedAt = new DateTime($dto->updated_at, new DateTimeZone('UTC'));
        }
    }

    /**
     * Initializes a new user instance.
     */
    public function __construct()
    {
        $now = new DateTime('now', new DateTimeZone('UTC'));
        $this->uuid = Uuid::fromString(Uuid::NIL);
        $this->createdAt = $now;
        $this->updatedAt = $now;
    }

    #region Getters and setters

    /**
     * Get the ID of the user.
     *
     * @return int The ID of the user.
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Get the UUID of the user.
     *
     * @return UuidInterface The UUID of the user.
     */
    public function getUuid(): UuidInterface
    {
        return $this->uuid;
    }

    /**
     * Get the username of the user.
     *
     * @return string The username of the user.
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * Get the email of the user.
     *
     * @return string The email of the user.
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Get the role of the user.
     *
     * @return string The role of the user.
     */
    public function getRole(): string
    {
        return $this->role;
    }

    /**
     * Get the status of the user.
     *
     * @return string The status of the user.
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * Get the date and time when the user was created.
     *
     * @return DateTime The date and time when the user was created.
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * Get the date and time when the user was last updated.
     *
     * @return DateTime The date and time when the user was last updated.
     */
    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    /**
     * Get the new password of the user.
     *
     * @return string The new password of the user.
     */
    public function getNewPassword(): string
    {
        return $this->newPassword;
    }

    /**
     * Sets the username of the user.
     *
     * @param string $username The username of the user.
     *
     * @return static The instance of the class.
     */
    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Sets the email of the user.
     *
     * @param string $email The email of the user.
     *
     * @return static The instance of the class.
     */
    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Sets the role of the user.
     *
     * @param string $role The role of the user.
     *
     * @return static The instance of the class.
     */
    public function setRole(string $role): static
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Sets the status of the user.
     *
     * @param string $status The status of the user.
     *
     * @return static The instance of the class.
     */
    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Sets the new password of the user.
     *
     * @param string $newPassword The new password of the user.
     *
     * @return static The instance of the class.
     */
    public function setNewPassword(string $newPassword): static
    {
        $this->newPassword = $newPassword;

        return $this;
    }

    #endregion Getters and setters
}
