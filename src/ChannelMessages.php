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

use ArrayIterator;
use Traversable;

/**
 * The channel messages.
 */
class ChannelMessages implements ChannelMessagesInterface
{
    /**
     * @var array<array-key, ChannelMessageInterface>
     */
    protected array $messages = [];
    
    /**
     * Create a new ChannelMessages.
     *
     * @param RecipientInterface $recipient
     * @param NotificationInterface $notification
     */
    public function __construct(
        protected RecipientInterface $recipient,
        protected NotificationInterface $notification,
    ) {}

    /**
     * Returns the recipient.
     *
     * @return RecipientInterface
     */
    public function recipient(): RecipientInterface
    {
        return $this->recipient;
    }
    
    /**
     * Returns the notification.
     *
     * @return NotificationInterface
     */
    public function notification(): NotificationInterface
    {
        return $this->notification;
    }
    
    /**
     * Add a message.
     *
     * @param ChannelMessageInterface $message
     * @return static $this
     */
    public function add(ChannelMessageInterface $message): static
    {
        $this->messages[$message->channel()] = $message;
        
        return $this;
    }
    
    /**
     * Returns true if has channel message, otherwise false.
     *
     * @param string $channel
     * @return bool
     */
    public function has(string $channel): bool
    {
        return array_key_exists($channel, $this->messages);
    }
    
    /**
     * Returns the channel message if exists, otherwise null.
     *
     * @param string $channel
     * @return null|ChannelMessageInterface
     */
    public function get(string $channel): null|ChannelMessageInterface
    {
        return $this->messages[$channel] ?? null;
    }
    
    /**
     * Returns all messages.
     *
     * @return iterable<array-key, ChannelMessageInterface>
     */
    public function all(): iterable
    {
        return $this->messages;
    }
    
    /**
     * Returns a new instance with the successful messages filtered.
     *
     * @return static
     */
    public function successful(): static
    {
        return $this->filter(fn(ChannelMessageInterface $m): bool => $m->isSuccessful());
    }
    
    /**
     * Returns a new instance with the failed messages filtered.
     *
     * @return static
     */
    public function failed(): static
    {
        return $this->filter(fn(ChannelMessageInterface $m): bool => $m->isFailure());
    }
    
    /**
     * Returns the channel names.
     *
     * @return array<int, string>
     */
    public function channelNames(): array
    {
        return array_keys($this->messages);
    }
    
    /**
     * Returns a new instance with the filtered messages.
     *
     * @param callable $callback
     * @return static
     */
    public function filter(callable $callback): static
    {
        $new = clone $this;
        $new->messages = array_filter($this->messages, $callback);
        return $new;
    }
    
    /**
     * Returns the number of messages.
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->messages);
    }
    
    /**
     * Returns the iterator. 
     *
     * @return Traversable
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->messages);
    }
}