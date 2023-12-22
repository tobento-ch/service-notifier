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

use Tobento\Service\Notifier\Exception\ChannelNotFoundException;
use Tobento\Service\Notifier\Exception\ChannelException;

/**
 * Channels
 */
class Channels implements ChannelsInterface
{
    /**
     * @var array<string, ChannelInterface>
     */
    protected array $channels = [];
    
    /**
     * Create a new Channels.
     *
     * @param ChannelInterface ...$channels
     */
    public function __construct(
        ChannelInterface ...$channels,
    ) {
        foreach($channels as $channel) {
            $this->add($channel);
        }
    }
    
    /**
     * Add a channel.
     *
     * @param ChannelInterface $channel
     * @return static $this
     */
    public function add(ChannelInterface $channel): static
    {
        $this->channels[$channel->name()] = $channel;
        return $this;
    }
    
    /**
     * Returns true if the channel exists, otherwise false.
     *
     * @param string $name The channel name.
     * @return bool
     */
    public function has(string $name): bool
    {
        return array_key_exists($name, $this->channels);
    }
    
    /**
     * Returns the channel if exists, otherwise null.
     *
     * @param string $name
     * @return ChannelInterface
     * @throws ChannelException
     */
    public function get(string $name): ChannelInterface
    {
        return $this->channels[$name] ?? throw new ChannelNotFoundException($name);
    }
    
    /**
     * Returns all channel names.
     *
     * @return array
     */
    public function names(): array
    {
        return array_keys($this->channels);
    }
}