<?php

declare(strict_types=1);

namespace Manychois\Butsing\Events;

use Psr\EventDispatcher\StoppableEventInterface;

/**
 * Represents the unit of communication between an emitter and appropriate listeners.
 */
abstract class AbstractEvent implements StoppableEventInterface
{
    private bool $propagationStopped = false;

    #region implements StoppableEventInterface

    /**
     * @inheritDoc
     */
    public function isPropagationStopped(): bool
    {
        return $this->propagationStopped;
    }

    #endregion implements StoppableEventInterface

    /**
     * Stops the propagation of the event to further listeners.
     */
    public function stopPropagation(): void
    {
        $this->propagationStopped = true;
    }
}
