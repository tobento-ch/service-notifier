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

namespace Tobento\Service\Notifier\Event;

use Tobento\Service\Notifier\ChannelMessagesInterface;

/**
 * NotificationSending
 */
final class NotificationSending
{
    /**
     * Create a new NotificationSending.
     *
     * @param ChannelMessagesInterface $messages
     */
    public function __construct(
        private ChannelMessagesInterface $messages,
    ) {}

    /**
     * Returns the channel messages.
     *
     * @return ChannelMessagesInterface
     */
    public function messages(): ChannelMessagesInterface
    {
        return $this->messages;
    }
}