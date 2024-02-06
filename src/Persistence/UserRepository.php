<?php

declare(strict_types=1);

namespace Manychois\Butsing\Persistence;

use InvalidArgumentException;
use Manychois\Butsing\Models\User;
use PDO;
use Ramsey\Uuid\Rfc4122\UuidV7;

/**
 * The repository for the user.
 */
class UserRepository
{
    private readonly PDO $pdo;

    /**
     * Initializes a new UserRepository instance.
     *
     * @param PDO $pdo The PDO instance.
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Gets a user by the login credentials.
     *
     * @param string $usernameOrEmail The username or email.
     * @param string $password        The password.
     *
     * @return null|User The user if login credentials are correct; otherwise, null.
     */
    public function getUserByLogin(string $usernameOrEmail, string $password): ?User
    {
        $sql = 'SELECT * FROM but_users WHERE username = :q OR email = :q LIMIT 1;';
        $stmt = $this->pdo->prepare($sql);
        \assert($stmt !== false);
        $stmt->execute(['q' => $usernameOrEmail]);
        $dto = $stmt->fetchObject(UserDto::class);
        if ($dto instanceof UserDto) {
            // phpcs:ignore Zend.NamingConventions.ValidVariableName.NotCamelCaps
            if (\password_verify($password, $dto->password_hash ?? '')) {
                $user = new User();
                User::setFromDto($user, $dto);

                return $user;
            }
        }

        return null;
    }

    /**
     * Creates a user in the database.
     *
     * @param User $user The user to be created.
     */
    public function createUser(User $user): void
    {
        $sql = <<<'SQL'
        INSERT INTO but_users
        (`uuid`, `username`, `email`, `password_hash`, `role`, `status`, `created_at`, `updated_at`)
        VALUES
        (:uuid, :username, :email, :passwordHash, :role, :status, NOW(6), NOW(6));
        SQL;
        $stmt = $this->pdo->prepare($sql);
        \assert($stmt !== false);
        if ($user->getId() !== 0) {
            throw new InvalidArgumentException('The user ID must be 0.');
        }
        if ($user->getNewPassword() === '') {
            throw new InvalidArgumentException('The new password is required.');
        }
        $passwordHash = \password_hash($user->getNewPassword(), \PASSWORD_DEFAULT);

        $stmt->execute([
            ':uuid' => UuidV7::uuid7()->getBytes(),
            ':username' => $user->getUsername(),
            ':email' => $user->getEmail(),
            ':passwordHash' => $passwordHash,
            ':role' => $user->getRole(),
            ':status' => $user->getStatus(),
        ]);
        $id = \intval($this->pdo->lastInsertId());

        $sql = 'SELECT * FROM but_users WHERE id = :id;';
        $stmt = $this->pdo->prepare($sql);
        \assert($stmt !== false);
        $stmt->execute(['id' => $id]);
        $dto = $stmt->fetchObject(UserDto::class);
        \assert($dto instanceof UserDto);
        User::setFromDto($user, $dto);
        $user->setNewPassword('');
    }
}
