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

/**
 * QueueHandlerInterface
 */
interface QueueHandlerInterface
{
    /**
     * Handle the notification.
     *
     * @param NotificationInterface $notification
     * @param RecipientInterface $recipient
     * @return void
     */
    public function handle(NotificationInterface $notification, RecipientInterface $recipient): void;
}