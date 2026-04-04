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

namespace Tobento\Service\Notifier\Browser;

use Psr\Clock\ClockInterface;
use Psr\Container\ContainerInterface;
use Tobento\Service\Notifier\ChannelInterface;
use Tobento\Service\Notifier\GuestRecipient;
use Tobento\Service\Notifier\NotificationInterface;
use Tobento\Service\Notifier\RecipientInterface;
use Tobento\Service\Notifier\Message;
use Tobento\Service\Notifier\Exception\UndefinedAddressException;
use Tobento\Service\Notifier\Exception\UndefinedMessageException;
use Tobento\Service\Repository\RepositoryInterface;
use Tobento\Service\Autowire\Autowire;

class Channel implements ChannelInterface
{
    /**
     * Create a new Channel.
     *
     * @param string $name
     * @param RepositoryInterface $repository
     * @param ClockInterface $clock
     * @param ContainerInterface $container
     */
    public function __construct(
        protected string $name,
        protected RepositoryInterface $repository,
        protected ClockInterface $clock,
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
     * Returns the repository.
     *
     * @return RepositoryInterface
     */
    public function repository(): RepositoryInterface
    {
        return $this->repository;
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
        if (! $recipient instanceof GuestRecipient && empty($recipient->getId())) {
            throw new UndefinedAddressException($this->name(), $notification, $recipient);
        }
        
        if (! $notification instanceof Message\ToBrowser) {
            throw new UndefinedMessageException($this->name(), $notification, $recipient);
        }
        
        $message = new Autowire($this->container)->call(
            $notification->toBrowserHandler(),
            ['recipient' => $recipient, 'channel' => $this->name()]
        );
        
        if (! $message instanceof Message\BrowserInterface) {
            throw new UndefinedMessageException($this->name(), $notification, $recipient);
        }
        
        $expiresAt = null;

        if ($recipient instanceof GuestRecipient) {
            $after = $recipient->getExpiresAfter();
            $now = $this->clock->now();

            if ($after instanceof \DateInterval) {
                $expiresAt = $now->add($after);
            } elseif (is_int($after)) {
                $modified = $now->modify('+'.$after.' seconds');
                $expiresAt = $modified === false ? null : $modified;
            }
        }
        
        return $this->repository->create([
            'name' => $notification->getName(),
            'recipient_id' => $recipient->getId(),
            'recipient_type' => $recipient->getType(),
            'data' => $message->getData(),
            //'read_at' => null,
            'expires_at' => $expiresAt,
            'created_at' => null,
        ]);
    }
}