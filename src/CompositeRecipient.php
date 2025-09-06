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

class CompositeRecipient implements RecipientInterface
{
    /**
     * @var array<array-key, RecipientInterface>
     */
    protected array $recipients = [];
    
    /**
     * Create a new CompositeRecipient.
     *
     * @param RecipientInterface ...$recipient
     */
    public function __construct(
        RecipientInterface ...$recipient,
    ) {
        $this->recipients = $recipient;
    }
    
    /**
     * Returns the recipients.
     *
     * @return array<array-key, RecipientInterface>
     */
    public function recipients(): array
    {
        return $this->recipients;
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
        foreach($this->recipients as $recipient) {
            if (!is_null($address = $recipient->getAddressForChannel(name: $name, notification: $notification))) {
                return $address;
            }
        }
        
        return null;
    }
    
    /**
     * Returns the channels the recipient wants to be notified.
     *
     * @param NotificationInterface $notification
     * @return array<int, string> ['mail', 'sms']
     */
    public function getChannels(NotificationInterface $notification): array
    {
        foreach($this->recipients as $recipient) {
            return $recipient->getChannels(notification: $notification);
        }
        
        return [];
    }
    
    /**
     * Returns the recipient locale.
     *
     * @return string
     */
    public function getLocale(): string
    {
        foreach($this->recipients as $recipient) {
            return $recipient->getLocale();
        }
        
        return 'en';
    }
    
    /**
     * Returns the recipient id.
     *
     * @return null|int|string
     */
    public function getId(): null|int|string
    {
        foreach($this->recipients as $recipient) {
            if (!is_null($id = $recipient->getId())) {
                return $id;
            }
        }
        
        return null;
    }
    
    /**
     * Returns the recipient type.
     *
     * @return string
     */
    public function getType(): string
    {
        foreach($this->recipients as $recipient) {
            return $recipient->getType();
        }
        
        return static::class;
    }
}