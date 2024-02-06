<?php

declare(strict_types=1);

namespace Manychois\Butsing\Core;

/**
 * Represents a plugin that can integrate with Butsing.
 */
interface PluginInterface
{
    /**
     * Registers listeners to the listener provider.
     *
     * @param ListenerProvider $listenerProvider The listener provider to register listeners to.
     */
    public function registerListeners(ListenerProvider $listenerProvider): void;
}
