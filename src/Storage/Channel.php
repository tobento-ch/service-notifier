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

namespace Tobento\Service\Notifier\Storage;

use Tobento\Service\Notifier\ChannelInterface;
use Tobento\Service\Notifier\NotificationInterface;
use Tobento\Service\Notifier\RecipientInterface;
use Tobento\Service\Notifier\Message;
use Tobento\Service\Notifier\Exception\UndefinedAddressException;
use Tobento\Service\Notifier\Exception\UndefinedMessageException;
use Tobento\Service\Repository\RepositoryInterface;
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
     * @param RepositoryInterface $repository
     * @param ContainerInterface $container
     */
    public function __construct(
        protected string $name,
        protected RepositoryInterface $repository,
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
        if (is_null($recipient->getId())) {
            throw new UndefinedAddressException($this->name(), $notification, $recipient);
        }
        
        if (! $notification instanceof Message\ToStorage) {
            throw new UndefinedMessageException($this->name(), $notification, $recipient);
        }
        
        $message = (new Autowire($this->container))->call(
            $notification->toStorageHandler(),
            ['recipient' => $recipient, 'channel' => $this->name()]
        );
        
        if (! $message instanceof Message\StorageInterface) {
            throw new UndefinedMessageException($this->name(), $notification, $recipient);
        }
        
        return $this->repository->create([
            'name' => $notification->getName(),
            'recipient_id' => $recipient->getId(),
            'recipient_type' => $recipient->getType(),
            'data' => $message->getData(),
            //'read_at' => null,
            'created_at' => null,
        ]);
    }
}