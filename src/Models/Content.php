<?php

declare(strict_types=1);

namespace Manychois\Butsing\Models;

use DateTime;

/**
 * Represents a content model in Butsing.
 */
class Content
{
    // @phpstan-ignore property.unused
    private int $id;
    // @phpstan-ignore property.unused
    private string $type;
    // @phpstan-ignore property.unused
    private string $name;
    // @phpstan-ignore property.unused
    private string $urlPart;
    // @phpstan-ignore property.unused
    private string $data;
    // @phpstan-ignore property.unused
    private DateTime $createdAt;
    // @phpstan-ignore property.unused
    private DateTime $updatedAt;
    // @phpstan-ignore property.unused
    private ?DateTime $deletedAt;
}
