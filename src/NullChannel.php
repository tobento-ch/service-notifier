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

use Tobento\Service\Notifier\Message\NullMessage;

/**
 * NullChannel: does not send a message.
 */
class NullChannel implements ChannelInterface
{
    /**
     * Create a new NullChannel.
     *
     * @param string $name
     */
    public function __construct(
        protected string $name = 'null',
    ) {}
    
    /**
     * Returns the channel name.
     *
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * Send the notification to the specified recipient.
     *
     * @param NotificationInterface $notification
     * @param RecipientInterface $recipient
     * @return NullMessage The sent message.
     * @throws \Throwable
     */
    public function send(NotificationInterface $notification, RecipientInterface $recipient): NullMessage
    {
        return new NullMessage();
    }
}