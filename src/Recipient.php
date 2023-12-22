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

use Tobento\Service\Notifier\Address\EmailInterface;
use Tobento\Service\Notifier\Address\Email;
use Tobento\Service\Notifier\Address\PhoneInterface;
use Tobento\Service\Notifier\Address\Phone;

/**
 * Recipient
 */
class Recipient implements RecipientInterface
{
    /**
     * @var array<string, mixed> The channel addresses.
     */
    protected array $addresses = [];
    
    /**
     * @var string
     */
    protected string $channelSeparator = '/';
    
    /**
     * Create a new Recipient.
     *
     * @param null|string|EmailInterface $email
     * @param null|string|PhoneInterface $phone
     * @param null|int|string $id
     * @param null|string $type
     * @param string $locale
     * @param array $channels
     */
    public function __construct(
        null|string|EmailInterface $email = null,
        null|string|PhoneInterface $phone = null,
        protected null|int|string $id = null,
        protected null|string $type = null,
        protected string $locale = 'en',
        protected array $channels = [],
    ) {
        if (is_string($email)) {
            $this->addAddress('mail', new Email($email));
        } elseif ($email instanceof EmailInterface) {
            $this->addAddress('mail', $email);
        }
        
        if (is_string($phone)) {
            $this->addAddress('sms', new Address\Phone($phone));
        } elseif ($phone instanceof PhoneInterface) {
            $this->addAddress('sms', $phone);
        }
    }
    
    /**
     * Add an address for the specified channel.
     *
     * @param string $channel The channel name
     * @param mixed $address
     * @return static $this
     */
    public function addAddress(string $channel, mixed $address): static
    {
        $this->addresses[$channel] = $address;
        return $this;
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
        if (array_key_exists($name, $this->addresses)) {
            return $this->addresses[$name];
        }
        
        $channelRoot = explode($this->channelSeparator, $name)[0];
        
        foreach($this->addresses as $channelName => $address) {
            if (str_starts_with($channelName, $channelRoot)) {
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
        return $this->type ?: static::class;
    }
}