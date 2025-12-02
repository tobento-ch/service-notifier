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

namespace Tobento\Service\Notifier\Address;

/**
 * Dsn: such as slack://TOKEN@default?channel=CHANNEL
 */
class Dsn implements DsnInterface
{
    /**
     * Create a new instance.
     *
     * @param string $dsn
     * @param null|string $name
     */
    public function __construct(
        protected string $dsn,
        protected null|string $name = null
    ) {}
    
    /**
     * Returns the dsn.
     *
     * @return string
     */
    public function dsn(): string
    {
        return $this->dsn;
    }
    
    /**
     * Returns the name.
     *
     * @return null|string
     */
    public function name(): null|string
    {
        return $this->name;
    }
}