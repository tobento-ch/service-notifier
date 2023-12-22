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

use IteratorAggregate;
use Countable;

/**
 * ChannelMessagesInterface
 */
interface ChannelMessagesInterface extends IteratorAggregate, Countable
{
    /**
     * Returns the recipient.
     *
     * @return RecipientInterface
     */
    public function recipient(): RecipientInterface;
    
    /**
     * Returns the notification.
     *
     * @return NotificationInterface
     */
    public function notification(): NotificationInterface;
    
    /**
     * Add a message.
     *
     * @param ChannelMessageInterface $message
     * @return static $this
     */
    public function add(ChannelMessageInterface $message): static;

    /**
     * Returns true if has channel message, otherwise false.
     *
     * @param string $channel
     * @return bool
     */
    public function has(string $channel): bool;
    
    /**
     * Returns the channel message if exists, otherwise null.
     *
     * @param string $channel
     * @return null|ChannelMessageInterface
     */
    public function get(string $channel): null|ChannelMessageInterface;
    
    /**
     * Returns all messages.
     *
     * @return iterable<array-key, ChannelMessageInterface>
     */
    public function all(): iterable;
    
    /**
     * Returns a new instance with the successful messages filtered.
     *
     * @return static
     */
    public function successful(): static;
    
    /**
     * Returns a new instance with the failed messages filtered.
     *
     * @return static
     */
    public function failed(): static;
    
    /**
     * Returns the channel names.
     *
     * @return array<int, string>
     */
    public function channelNames(): array;
    
    /**
     * Returns a new instance with the filtered messages.
     *
     * @param callable $callback
     * @return static
     */
    public function filter(callable $callback): static;
}