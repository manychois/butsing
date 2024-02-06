<?php

declare(strict_types=1);

namespace Manychois\Butsing\Core;

use Generator;
use InvalidArgumentException;
use RuntimeException;

/**
 * Represents a priority queue.
 *
 * @template TItem
 */
class PriorityQueue
{
    /**
     * @var array<int, array{0: TItem, 1: int}>
     */
    private array $queue = [];

    /**
     * Adds an item to the queue.
     *
     * @param TItem $item
     * @param int   $priority
     */
    public function enqueue($item, int $priority): void
    {
        $this->queue[] = [$item, $priority];
        \usort($this->queue, static fn ($a, $b) => -1 * ($a[1] <=> $b[1]));
    }

    /**
     * Removes and returns the item with the highest priority.
     *
     * @return TItem the item with the highest priority.
     */
    public function dequeue()
    {
        if (!isset($this->queue[0])) {
            throw new RuntimeException('The queue is empty.');
        }

        return \array_shift($this->queue)[0];
    }

    /**
     * Checks if the queue is empty.
     *
     * @return bool true if the queue is empty, false otherwise.
     */
    public function isEmpty(): bool
    {
        return \count($this->queue) === 0;
    }

    /**
     * Loops through the queue without removing items.
     *
     * @return Generator<array{0: TItem, 1: int}>
     */
    public function loop(): Generator
    {
        foreach ($this->queue as $item) {
            yield $item;
        }
    }

    /**
     * Removes an item from the queue.
     *
     * @param int $index The index of the item to remove.
     */
    public function removeAt(int $index): void
    {
        if (!isset($this->queue[$index])) {
            throw new InvalidArgumentException('The index is out of range.');
        }
        \array_splice($this->queue, $index, 1);
    }
}
