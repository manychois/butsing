<?php

declare(strict_types=1);

namespace Manychois\Butsing\Core;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\EventDispatcher\StoppableEventInterface;

/**
 * Retrieves Listeners from a Listener Provider for the Event dispatched, and invoking each Listener with that Event.
 */
class EventDispatcher implements EventDispatcherInterface
{
    private readonly ListenerProviderInterface $listenerProvider;

    /**
     * @param ListenerProviderInterface $listenerProvider The Listener Provider to use.
     */
    public function __construct(ListenerProviderInterface $listenerProvider)
    {
        $this->listenerProvider = $listenerProvider;
    }

    #region implements EventDispatcherInterface

    /**
     * @inheritDoc
     */
    public function dispatch(object $event): object
    {
        foreach ($this->listenerProvider->getListenersForEvent($event) as $listener) {
            $listener($event);
            if ($event instanceof StoppableEventInterface && $event->isPropagationStopped()) {
                break;
            }
        }

        return $event;
    }

    #endregion implements EventDispatcherInterface
}
