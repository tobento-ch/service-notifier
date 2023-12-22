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

namespace Tobento\Service\Notifier\Parameter;

/**
 * Queue
 */
class Queue extends Parameter
{
    /**
     * Create a new Queue.
     *
     * @param null|string $name A queue name
     * @param int $delay Delay in seconds
     * @param int $retry
     * @param int $priority
     * @param bool $encrypt
     */
    public function __construct(
        protected null|string $name = null,
        protected int $delay = 0,
        protected int $retry = 3,
        protected int $priority = 0,
        protected bool $encrypt = false,
    ) {}
    
    /**
     * Returns the queue name.
     *
     * @return null|string
     */
    public function name(): null|string
    {
        return $this->name;
    }
    
    /**
     * Set the queue name.
     *
     * @param string $name
     * @return static
     */
    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }
    
    /**
     * Returns the delay in seconds.
     *
     * @return int
     */
    public function delay(): int
    {
        return $this->delay;
    }
    
    /**
     * Returns the retry.
     *
     * @return int
     */
    public function retry(): int
    {
        return $this->retry;
    }
    
    /**
     * Returns the priority.
     *
     * @return int
     */
    public function priority(): int
    {
        return $this->priority;
    }
    
    /**
     * Returns true if to encrypt the message, otherwise false.
     *
     * @return bool
     */
    public function encrypt(): bool
    {
        return $this->encrypt;
    }
}