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

use Tobento\Service\Notifier\Exception\ChannelCreateException;

/**
 * ChannelFactoryInterface
 */
interface ChannelFactoryInterface
{
    /**
     * Create a new channel based on the configuration.
     *
     * @param string $name
     * @param array $config
     * @return ChannelInterface
     * @throws ChannelCreateException
     */
    public function createChannel(string $name, array $config = []): ChannelInterface;
}