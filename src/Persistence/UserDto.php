<?php

declare(strict_types=1);

namespace Manychois\Butsing\Persistence;

/**
 * The data transfer object for the user.
 */
class UserDto
{
    public ?int $id = null;
    public ?string $uuid = null;
    public ?string $username = null;
    public ?string $email = null;
    // phpcs:ignore Zend.NamingConventions.ValidVariableName.MemberVarNotCamelCaps
    public ?string $password_hash = null;
    public ?string $role = null;
    public ?string $status = null;
    // phpcs:ignore Zend.NamingConventions.ValidVariableName.MemberVarNotCamelCaps
    public ?string $created_at = null;
    // phpcs:ignore Zend.NamingConventions.ValidVariableName.MemberVarNotCamelCaps
    public ?string $updated_at = null;
}
