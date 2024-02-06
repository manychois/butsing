<?php

declare(strict_types=1);

namespace Manychois\Butsing\Events;

use Slim\App;

/**
 * Represents an event that is triggered after the default routing is set.
 * Plugins can use this event to add their own routes.
 */
class AfterRoutingSetEvent extends AbstractEvent
{
    public readonly App $slim;

    /**
     * Initializes a new instance of the AfterRoutingSetEvent class.
     *
     * @param App $slim The Slim application instance.
     */
    public function __construct(App $slim)
    {
        $this->slim = $slim;
    }
}
