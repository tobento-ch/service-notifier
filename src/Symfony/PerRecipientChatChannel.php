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

namespace Tobento\Service\Notifier\Symfony;

use Symfony\Component\Notifier\Channel\ChannelInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\Recipient\RecipientInterface;

class PerRecipientChatChannel implements ChannelInterface
{
    public function notify(Notification $notification, RecipientInterface $recipient, ?string $transportName = null): void
    {
        //
    }

    public function supports(Notification $notification, RecipientInterface $recipient): bool
    {
        return true;
    }
}