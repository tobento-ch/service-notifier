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

namespace Tobento\Service\Notifier\Mail;

use Tobento\Service\Notifier\ChannelInterface;
use Tobento\Service\Notifier\NotificationInterface;
use Tobento\Service\Notifier\RecipientInterface;
use Tobento\Service\Notifier\Message\ToMail;
use Tobento\Service\Notifier\Address;
use Tobento\Service\Notifier\Exception\UndefinedMessageException;
use Tobento\Service\Notifier\Exception\UndefinedAddressException;
use Tobento\Service\Mail\MailerInterface;
use Tobento\Service\Mail\Message;
use Tobento\Service\Mail\Address as Adr;
use Tobento\Service\Autowire\Autowire;
use Psr\Container\ContainerInterface;

/**
 * Channel
 */
class Channel implements ChannelInterface
{
    /**
     * Create a new Channel.
     *
     * @param string $name
     * @param MailerInterface $mailer
     * @param ContainerInterface $container
     */
    public function __construct(
        protected string $name,
        protected MailerInterface $mailer,
        protected ContainerInterface $container,
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
     * Returns the mailer.
     *
     * @return MailerInterface
     */
    public function mailer(): MailerInterface
    {
        return $this->mailer;
    }

    /**
     * Send the notification to the specified recipient.
     *
     * @param NotificationInterface $notification
     * @param RecipientInterface $recipient
     * @return Message The sent message.
     * @throws \Throwable
     */
    public function send(NotificationInterface $notification, RecipientInterface $recipient): Message
    {
        if (! $notification instanceof ToMail) {
            throw new UndefinedMessageException($this->name(), $notification, $recipient);
        }
        
        $message = (new Autowire($this->container))->call(
            $notification->toMailHandler(),
            ['recipient' => $recipient, 'channel' => $this->name()]
        );
        
        if (! $message instanceof Message) {
            throw new UndefinedMessageException(
                channel: $this->name(),
                notification: $notification,
                recipient: $recipient,
                message: sprintf('Mail message needs to be an instanceof %s', Message::class),
            );
        }
        
        // Ensure to address:        
        if ($message->getTo()->empty()) {
            $address = $recipient->getAddressForChannel(name: $this->name(), notification: $notification);

            if (! $address instanceof Address\EmailInterface) {
                throw new UndefinedAddressException($this->name(), $notification, $recipient);
            }
            
            $message->to(new Adr($address->email(), $address->name()));
        }
        
        $this->mailer->send($message);
        
        return $message;
    }
}