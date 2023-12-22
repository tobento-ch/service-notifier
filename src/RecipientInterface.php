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

use Tobento\Service\Notifier\Address\AddressInterface;

/**
 * RecipientInterface
 */
interface RecipientInterface
{
    /**
     * Returns the address for the specified channel or null if none.
     *
     * @param string $name The channel name.
     * @param NotificationInterface $notification
     * @return mixed
     */
    public function getAddressForChannel(string $name, NotificationInterface $notification): mixed;
    
    /**
     * Returns the channels the recipient wants to be notified.
     *
     * @param NotificationInterface $notification
     * @return array<int, string> ['mail', 'sms']
     */
    public function getChannels(NotificationInterface $notification): array;
    
    /**
     * Returns the recipient locale.
     *
     * @return string
     */
    public function getLocale(): string;
    
    /**
     * Returns the recipient id.
     *
     * @return null|int|string
     */
    public function getId(): null|int|string;
    
    /**
     * Returns the recipient type.
     *
     * @return string
     */
    public function getType(): string;
}