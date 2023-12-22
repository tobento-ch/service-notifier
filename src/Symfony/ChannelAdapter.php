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

use Tobento\Service\Notifier\ChannelInterface;
use Tobento\Service\Notifier\NotificationInterface;
use Tobento\Service\Notifier\RecipientInterface;
use Tobento\Service\Notifier\Message;
use Tobento\Service\Notifier\Address;
use Tobento\Service\Notifier\Exception\ChannelException;
use Tobento\Service\Notifier\Exception\UndefinedMessageException;
use Tobento\Service\Notifier\Exception\UndefinedAddressException;
use Symfony\Component\Notifier\Channel\ChannelInterface as SymfonyChannelInterface;
use Symfony\Component\Notifier\Channel\ChatChannel;
use Symfony\Component\Notifier\Message\ChatMessage;
use Symfony\Component\Notifier\Channel\PushChannel;
use Symfony\Component\Notifier\Message\PushMessage;
use Symfony\Component\Notifier\Channel\SmsChannel;
use Symfony\Component\Notifier\Message\SmsMessage;
use Symfony\Component\Notifier\Recipient\RecipientInterface as SymfonyRecipientInterface;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Component\Notifier\Recipient\NoRecipient;
use Tobento\Service\Autowire\Autowire;
use Psr\Container\ContainerInterface;
use Exception;

/**
 * ChannelAdapter
 */
class ChannelAdapter implements ChannelInterface
{
    /**
     * Create a new ChannelAdapter.
     *
     * @param string $name A channel name.
     * @param SymfonyChannelInterface $channel
     * @param ContainerInterface $container
     * @param array $config
     */
    public function __construct(
        protected string $name,
        protected SymfonyChannelInterface $channel,
        protected ContainerInterface $container,
        protected array $config = [],
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
     * Returns the channel.
     *
     * @return SymfonyChannelInterface
     */
    public function channel(): SymfonyChannelInterface
    {
        return $this->channel;
    }
    
    /**
     * Send the notification to the specified recipient.
     *
     * @param NotificationInterface $notification
     * @param RecipientInterface $recipient
     * @return object The sent message.
     * @throws \Throwable
     */
    public function send(NotificationInterface $notification, RecipientInterface $recipient): object
    {
        $notification = $this->createSymfonyNotification($notification, $recipient);
        
        try {
            $this->channel->notify($notification, $notification->getRecipient());
            return $notification->getMessage();
        } catch (ChannelException $e) {
            throw $e;
        } catch (Exception $e) {
            throw new ChannelException(
                message: $e->getMessage(),
                code: 0,
                previous: $e,
            );
        }
    }
    
    /**
     * Create the symfony notification from the notification.
     *
     * @param NotificationInterface $notification
     * @param RecipientInterface $recipient
     * @return Notification
     * @throws ChannelException
     */
    protected function createSymfonyNotification(
        NotificationInterface $notification,
        RecipientInterface $recipient
    ): Notification {
        switch (true) {
            case $this->channel instanceof SmsChannel:
                return $this->createSmsNotification($notification, $recipient);
            case $this->channel instanceof ChatChannel:
                return $this->createChatNotification($notification, $recipient);
            case $this->channel instanceof PushChannel:
                return $this->createPushNotification($notification, $recipient);
        }
        
        throw new ChannelException(sprintf('Symfony channel %s is not supported', $this->channel::class));
    }
    
    /**
     * Create sms notification.
     *
     * @param NotificationInterface $notification
     * @param RecipientInterface $recipient
     * @return Notification
     * @throws ChannelException
     */
    protected function createSmsNotification(
        NotificationInterface $notification,
        RecipientInterface $recipient
    ): Notification {
        if (! $notification instanceof Message\ToSms) {
            throw new UndefinedMessageException($this->name(), $notification, $recipient);
        }
        
        $message = (new Autowire($this->container))->call(
            $notification->toSmsHandler(),
            ['recipient' => $recipient, 'channel' => $this->name()]
        );
        
        if (! $message instanceof Message\SmsInterface) {
            throw new UndefinedMessageException(
                channel: $this->name(),
                notification: $notification,
                recipient: $recipient,
                message: sprintf('Sms message needs to be an instanceof %s', Message\SmsInterface::class),
            );
        }
        
        if (is_null($message->getTo())) {
            $address = $recipient->getAddressForChannel(name: $this->name(), notification: $notification);

            if (! $address instanceof Address\PhoneInterface) {
                throw new UndefinedAddressException($this->name(), $notification, $recipient);
            }
            
            $message->to($address);
        }
        
        /*
        // for next version:
        $from = '';
        if ($message->getFrom()) {
            $from = $message->getFrom()->name() ?: $message->getFrom()->phone();
        }*/
        
        return new Notification(
            recipient: new Recipient(phone: $message->getTo()->phone()),
            message: new SmsMessage(
                phone: $message->getTo()->phone(),
                subject: $message->getSubject(),
                // from: $from, // since version: 6.3
            ),
        );
    }
    
    /**
     * Create chat notification.
     *
     * @param NotificationInterface $notification
     * @param RecipientInterface $recipient
     * @return Notification
     * @throws ChannelException
     */
    protected function createChatNotification(
        NotificationInterface $notification,
        RecipientInterface $recipient
    ): Notification {
        if (! $notification instanceof Message\ToChat) {
            throw new UndefinedMessageException($this->name(), $notification, $recipient);
        }
        
        $message = (new Autowire($this->container))->call(
            $notification->toChatHandler(),
            ['recipient' => $recipient, 'channel' => $this->name()]
        );
        
        if (! $message instanceof Message\ChatInterface) {
            throw new UndefinedMessageException(
                channel: $this->name(),
                notification: $notification,
                recipient: $recipient,
                message: sprintf('Chat message needs to be an instanceof %s', Message\ChatInterface::class),
            );
        }
        
        return new Notification(
            recipient: new NoRecipient(),
            message: new ChatMessage(
                subject: $message->getSubject(),
                options: $message->parameters()->name(MessageOptions::class)->first()?->getOptions(),
            ),
        );
    }
    
    /**
     * Create push notification.
     *
     * @param NotificationInterface $notification
     * @param RecipientInterface $recipient
     * @return Notification
     * @throws ChannelException
     */
    protected function createPushNotification(
        NotificationInterface $notification,
        RecipientInterface $recipient
    ): Notification {
        if (! $notification instanceof Message\ToPush) {
            throw new UndefinedMessageException($this->name(), $notification, $recipient);
        }
        
        $message = (new Autowire($this->container))->call(
            $notification->toPushHandler(),
            ['recipient' => $recipient, 'channel' => $this->name()]
        );
        
        if (! $message instanceof Message\PushInterface) {
            throw new UndefinedMessageException(
                channel: $this->name(),
                notification: $notification,
                recipient: $recipient,
                message: sprintf('Push message needs to be an instanceof %s', Message\PushInterface::class),
            );
        }
        
        return new Notification(
            recipient: new NoRecipient(),
            message: new PushMessage(
                subject: $message->getSubject(),
                content: $message->getSubject(),
                options: $message->parameters()->name(MessageOptions::class)->first()?->getOptions(),
            ),
        );
    }
}