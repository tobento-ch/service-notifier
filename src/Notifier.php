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

use Tobento\Service\Notifier\Message;
use Tobento\Service\Notifier\Exception\NotifierException;
use Tobento\Service\Notifier\Exception\ChannelException;
use Tobento\Service\Notifier\Exception\UndefinedMessageException;
use Tobento\Service\Notifier\Exception\UndefinedAddressException;
use Psr\EventDispatcher\EventDispatcherInterface;
use Throwable;

/**
 * Notifier
 */
class Notifier implements NotifierInterface
{
    /**
     * Create a new Notifier.
     *
     * @param ChannelsInterface $channels
     * @param null|QueueHandlerInterface $queueHandler
     * @param null|EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        protected ChannelsInterface $channels,
        protected null|QueueHandlerInterface $queueHandler = null,
        protected null|EventDispatcherInterface $eventDispatcher = null,
    ) {}
    
    /**
     * Send the notification to the specified recipients.
     *
     * @param NotificationInterface $notification
     * @param RecipientInterface ...$recipients
     * @return iterable<int, ChannelMessagesInterface>
     * @throws NotifierException
     */
    public function send(NotificationInterface $notification, RecipientInterface ...$recipients): iterable
    {
        $queue = $notification->parameters()->name(Parameter\Queue::class)->first();
        
        if (
            $this->queueHandler
            && !is_null($queue)
        ) {
            foreach($recipients as $recipient) {
                $this->queueHandler->handle($notification, $recipient);
                $this->eventDispatcher?->dispatch(new Event\NotificationQueued($notification, $recipient));
            }
            
            return [];
        }
        
        $channelMessages = [];
        
        foreach($recipients as $recipient) {
            
            $messages = new ChannelMessages($recipient, $notification);
            
            $this->eventDispatcher?->dispatch(new Event\NotificationSending($messages));
            
            foreach($this->getChannels($notification, $recipient) as $channel) {
                try {
                    $message = $channel->send($notification, $recipient);
                    $messages->add(new ChannelMessage($channel->name(), $message));
                } catch (UndefinedMessageException|UndefinedAddressException $e) {
                    // ignore as notification or recipient may not support that channel.
                    $messages->add(new ChannelMessage($channel->name(), null, $e));
                } catch (Throwable $e) {
                    throw new NotifierException($e->getMessage(), (int)$e->getCode(), $e);
                }
            }

            $this->eventDispatcher?->dispatch(new Event\NotificationSent($messages));
            
            $channelMessages[] = $messages;
        }
        
        return $channelMessages;
    }
    
    /**
     * Returns the channels for the specified notification.
     *
     * @param NotificationInterface $notification
     * @param RecipientInterface $recipient
     * @return iterable<string, ChannelInterface>
     */
    protected function getChannels(NotificationInterface $notification, RecipientInterface $recipient): iterable
    {
        $channels = $notification->getChannels($recipient);
        
        if (empty($channels)) {
            // if no specified we send to all channels.
            $channels = $this->channels->names();
        }
        
        foreach($channels as $channel) {
            
            if (! $this->channels->has(name: $channel)) {
                // we might throw exception instead, but for now we just continue.
                continue;
            }
            
            try {
                $channel = $this->channels->get(name: $channel);
            } catch (Throwable $e) {
                throw new NotifierException($e->getMessage(), (int)$e->getCode(), $e);
            }
            
            yield $channel->name() => $channel;
        }
    }
}