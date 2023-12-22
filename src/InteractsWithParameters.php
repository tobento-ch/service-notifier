<?php

/**
 * TOBENTO
 *
 * @copyright   Tobias Strub, TOBENTO
 * @license     MIT License, see LICENSE file distributed with this source code.
 * @author      Tobias Strub
 * @link        https://www.tobento.ch
 */

declare(strict_types=1);

namespace Tobento\Service\Notifier;

use Tobento\Service\Notifier\Parameter;

/**
 * Default parameters methods.
 */
trait InteractsWithParameters
{
    /**
     * Specify the queue.
     *
     * @param null|string $name A queue name
     * @param int $delay Delay in seconds
     * @param int $retry
     * @param int $priority
     * @param bool $encrypt
     * @return static $this
     */
    public function queue(
        null|string $name = null,
        int $delay = 0,
        int $retry = 3,
        int $priority = 0,
        bool $encrypt = false,    
    ): static {
        $this->parameters()->add(new Parameter\Queue(
            name: $name,
            delay: $delay,
            priority: $priority,
            encrypt: $encrypt
        ));
        
        return $this;
    }
}