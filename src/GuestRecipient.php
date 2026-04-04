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

use DateInterval;

class GuestRecipient implements RecipientInterface
{
    /**
     * Create a new GuestRecipient.
     *
     * @param null|string $id
     * @param string $locale
     * @param array $channels
     */
    public function __construct(
        protected null|string $id = null,
        protected null|int|DateInterval $expiresAfter = null,
        protected string $locale = 'en',
        protected array $channels = [],
    ) {}
    
    /**
     * Returns the expires after.
     *
     * @return null|int|DateInterval
     */
    public function getExpiresAfter(): null|int|DateInterval
    {
        return $this->expiresAfter;
    }
    
    /**
     * Returns the address for the specified channel or null if none.
     *
     * @param string $name The channel name.
     * @param NotificationInterface $notification
     * @return mixed
     */
    public function getAddressForChannel(string $name, NotificationInterface $notification): mixed
    {
        if (!str_starts_with($name, 'browser')) {
            return null;
        }
        
        return $this->id;
    }
    
    /**
     * Returns the channels the recipient wants to be notified.
     *
     * @param NotificationInterface $notification
     * @return array<int, string> ['mail', 'sms']
     */
    public function getChannels(NotificationInterface $notification): array
    {
        return $this->channels;
    }
    
    /**
     * Returns the recipient locale.
     *
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }
    
    /**
     * Returns the recipient id.
     *
     * @return null|int|string
     */
    public function getId(): null|int|string
    {
        return $this->id;
    }
    
    /**
     * Returns the recipient type.
     *
     * @return string
     */
    public function getType(): string
    {
        return static::class;
    }
}