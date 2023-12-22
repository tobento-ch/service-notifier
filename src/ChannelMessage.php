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

use Throwable;

/**
 * ChannelMessage
 */
class ChannelMessage implements ChannelMessageInterface
{
    /**
     * Create a new ChannelMessage.
     *
     * @param string $channel The channel name.
     * @param null|object $message
     * @param null|Throwable $exception
     */
    public function __construct(
        protected string $channel,
        protected null|object $message = null,
        protected null|Throwable $exception = null,
    ) {}
    
    /**
     * Returns the channel name.
     *
     * @return string
     */
    public function channel(): string
    {
        return $this->channel;
    }
    
    /**
     * Returns the message.
     *
     * @return null|object
     */
    public function message(): null|object
    {
        return $this->message;
    }
    
    /**
     * Returns the exception.
     *
     * @return null|Throwable
     */
    public function exception(): null|Throwable
    {
        return $this->exception;
    }
    
    /**
     * Returns true if the message was successful, otherwise false.
     *
     * @return bool
     */
    public function isSuccessful(): bool
    {
        return is_null($this->exception());
    }
    
    /**
     * Returns true if the message failed, otherwise false.
     *
     * @return bool
     */
    public function isFailure(): bool
    {
        return !is_null($this->exception());
    }
}