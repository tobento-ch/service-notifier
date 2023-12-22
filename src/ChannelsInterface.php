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

use Tobento\Service\Notifier\Exception\ChannelException;

/**
 * ChannelsInterface
 */
interface ChannelsInterface
{
    /**
     * Returns true if channel exists, otherwise false.
     *
     * @param string $name
     * @return bool
     */
    public function has(string $name): bool;
    
    /**
     * Returns the channel if exists, otherwise null.
     *
     * @param string $name
     * @return ChannelInterface
     * @throws ChannelException
     */
    public function get(string $name): ChannelInterface;
    
    /**
     * Returns all channel names.
     *
     * @return array
     */
    public function names(): array;
}