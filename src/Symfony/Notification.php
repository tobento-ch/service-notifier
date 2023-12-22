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

use Symfony\Component\Notifier\Notification\Notification as SymfonyNotification;
use Symfony\Component\Notifier\Notification\SmsNotificationInterface;
use Symfony\Component\Notifier\Notification\ChatNotificationInterface;
use Symfony\Component\Notifier\Notification\PushNotificationInterface;
use Symfony\Component\Notifier\Message\MessageInterface;
use Symfony\Component\Notifier\Message\SmsMessage;
use Symfony\Component\Notifier\Message\ChatMessage;
use Symfony\Component\Notifier\Message\PushMessage;
use Symfony\Component\Notifier\Recipient\RecipientInterface;
use Symfony\Component\Notifier\Recipient\SmsRecipientInterface;

/**
 * Notification
 */
class Notification extends SymfonyNotification implements
    SmsNotificationInterface,
    ChatNotificationInterface,
    PushNotificationInterface
{
    /**
     * Create a new Notification.
     *
     * @param RecipientInterface $recipient
     * @param MessageInterface $message
     */
    public function __construct(
        protected RecipientInterface $recipient,
        protected MessageInterface $message,
    ) {}
    
    public function getRecipient(): RecipientInterface
    {
        return $this->recipient;
    }

    public function getMessage(): MessageInterface
    {
        return $this->message;
    }
    
    public function asSmsMessage(SmsRecipientInterface $recipient, string $transport = null): ?SmsMessage
    {
        return $this->message instanceof SmsMessage ? $this->message : null;
    }
    
    public function asChatMessage(RecipientInterface $recipient, string $transport = null): ?ChatMessage
    {
        return $this->message instanceof ChatMessage ? $this->message : null;
    }
    
    public function asPushMessage(RecipientInterface $recipient, string $transport = null): ?PushMessage
    {
        return $this->message instanceof PushMessage ? $this->message : null;
    }
}