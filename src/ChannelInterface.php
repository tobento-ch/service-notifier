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
 * ChannelInterface
 */
interface ChannelInterface
{
    /**
     * Returns the channel name.
     *
     * @return string
     */
    public function name(): string;
    
    /**
     * Send the notification to the specified recipient.
     *
     * @param NotificationInterface $notification
     * @param RecipientInterface $recipient
     * @return object The sent message.
     * @throws ChannelException
     */
    public function send(NotificationInterface $notification, RecipientInterface $recipient): object;
}