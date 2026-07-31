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

use Tobento\Service\Notifier\Exception\NotifierException;

final class CompositeNotifier implements NotifierInterface
{
    /**
     * @var array<int, NotifierInterface>
     */
    private array $notifiers;

    /**
     * Create a new instance..
     *
     * @param NotifierInterface ...$notifiers
     */
    public function __construct(NotifierInterface ...$notifiers)
    {
        $this->notifiers = $notifiers;
    }

    /**
     * Send the notification using all notifiers.
     *
     * @param NotificationInterface $notification
     * @param RecipientInterface ...$recipients
     * @return iterable<int, ChannelMessagesInterface>
     * @throws NotifierException
     */
    public function send(NotificationInterface $notification, RecipientInterface ...$recipients): iterable
    {
        $allMessages = [];

        foreach ($this->notifiers as $notifier) {
            // Each notifier returns iterable<ChannelMessagesInterface>
            foreach ($notifier->send($notification, ...$recipients) as $messages) {
                $allMessages[] = $messages;
            }
        }

        return $allMessages;
    }
}