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

namespace Tobento\Service\Notifier\Event;

use Tobento\Service\Notifier\NotificationInterface;
use Tobento\Service\Notifier\RecipientInterface;

/**
 * NotificationQueued
 */
final class NotificationQueued
{
    /**
     * Create a new NotificationQueued.
     *
     * @param NotificationInterface $notification
     * @param RecipientInterface $recipient
     */
    public function __construct(
        private NotificationInterface $notification,
        private RecipientInterface $recipient
    ) {}

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
     * Returns the recipient.
     *
     * @return RecipientInterface
     */
    public function recipient(): RecipientInterface
    {
        return $this->recipient;
    }
}