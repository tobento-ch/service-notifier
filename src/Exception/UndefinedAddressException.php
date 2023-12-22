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

namespace Tobento\Service\Notifier\Exception;

use Tobento\Service\Notifier\NotificationInterface;
use Tobento\Service\Notifier\RecipientInterface;
use Throwable;

/**
 * UndefinedAddressException
 */
class UndefinedAddressException extends ChannelException
{
    /**
     * Create a new UndefinedAddressException.
     *
     * @param string $channel
     * @param NotificationInterface $notification
     * @param RecipientInterface $recipient
     * @param string $message The message
     * @param int $code
     * @param null|Throwable $previous
     */
    public function __construct(
        protected string $channel,
        protected NotificationInterface $notification,
        protected RecipientInterface $recipient,
        string $message = '',
        int $code = 0,
        null|Throwable $previous = null
    ) {
        if (empty($message)) {
            $message = sprintf(
                'Notification %s has no address for the channel %s defined',
                $notification::class,
                $channel,
            );
        }
        
        parent::__construct($message, $code, $previous);
    }
    
    /**
     * Returns the channel.
     *
     * @return string
     */
    public function channel(): string
    {
        return $this->channel;
    }
    
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