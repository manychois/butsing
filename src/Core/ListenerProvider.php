<?php

declare(strict_types=1);

namespace Manychois\Butsing\Core;

use Generator;
use Psr\EventDispatcher\ListenerProviderInterface;

/**
 * Determines what listeners are relevant to and should be called for a given event,
 * and provides registration mechanism for listeners to be associated with events.
 */
class ListenerProvider implements ListenerProviderInterface
{
    /**
     * @var array<string, PriorityQueue<callable>>
     */
    private array $listeners = [];

    #region implements ListenerProviderInterface

    /**
     * @inheritDoc
     *
     * @return iterable<callable>
     */
    public function getListenersForEvent(object $event): iterable
    {
        return $this->getListenersByClassName(\get_class($event));
    }

    #endregion implements ListenerProviderInterface

    /**
     * Adds a Listener to the ListenerProvider.
     *
     * @param string   $eventClass The class name of the Event.
     * @param callable $listener   The Listener to add.
     * @param int      $priority   The priority of the Listener. Higher priority Listeners are called first.
     *
     * @return bool true if the Listener is added, false otherwise.
     */
    public function addListener(string $eventClass, callable $listener, int $priority = 10): bool
    {
        if (isset($this->listeners[$eventClass])) {
            $pq = $this->listeners[$eventClass];
            // @phpstan-ignore function.alreadyNarrowedType
            \is_callable($listener, false, $callableName);
            foreach ($pq->loop() as $item) {
                $fn = $item[0];
                if ($fn === $listener) {
                    return false;
                }
                \is_callable($item[0], false, $itemCallableName);
                if ($callableName === $itemCallableName) {
                    return false;
                }
            }
        } else {
            $pq = new PriorityQueue();
            $this->listeners[$eventClass] = $pq;
        }

        $pq->enqueue($listener, $priority);

        return true;
    }

    /**
     * Removes a Listener from the ListenerProvider.
     *
     * @param string   $eventClass The class name of the Event.
     * @param callable $listener   The Listener to remove.
     *
     * @return bool true if the Listener is removed, false otherwise.
     */
    public function removeListener(string $eventClass, callable $listener): bool
    {
        if (!isset($this->listeners[$eventClass])) {
            return false;
        }

        $pq = $this->listeners[$eventClass];
        // @phpstan-ignore function.alreadyNarrowedType
        \is_callable($listener, false, $callableName);
        $found = -1;
        foreach ($pq->loop() as $i => $item) {
            /**
             * @var int $i
             */
            $fn = $item[0];
            if ($fn === $listener) {
                $found = $i;
                break;
            }
            \is_callable($item[0], false, $itemCallableName);
            if ($callableName === $itemCallableName) {
                $found = $i;
                break;
            }
        }

        if ($found >= 0) {
            $pq->removeAt($found);

            return true;
        }

        return false;
    }

    /**
     * Returns the Listeners for the specified Event class name.
     *
     * @param string $eventClass The class name of the Event.
     *
     * @return Generator<callable> The Listeners for the specified Event class name.
     */
    private function getListenersByClassName(string $eventClass): Generator
    {
        if (isset($this->listeners[$eventClass])) {
            $pq = $this->listeners[$eventClass];
            $i = 0;
            foreach ($pq->loop() as $item) {
                yield $i => $item[0];
                ++$i;
            }

            $parentClass = \get_parent_class($eventClass);
            if ($parentClass !== false) {
                foreach ($this->getListenersByClassName($parentClass) as $item) {
                    yield $i => $item;
                    ++$i;
                }
            }
        }
    }
}
