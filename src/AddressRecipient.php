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
use Tobento\Service\User\AddressInterface;

class AddressRecipient implements RecipientInterface
{
    /**
     * Create a new AddressRecipient.
     *
     * @param AddressInterface $address
     * @param array $channels
     * @param null|string $type
     */
    public function __construct(
        protected AddressInterface $address,
        protected array $channels = [],
        protected null|string $type = null,
    ) {}
    
    /**
     * Returns the address.
     *
     * @return AddressInterface
     */
    public function address(): AddressInterface
    {
        return $this->address;
    }
    
    /**
     * Returns the type.
     *
     * @return null|string
     */
    public function type(): null|string
    {
        return $this->type;
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
        return match (true) {
            str_starts_with($name, 'mail') && $this->address()->email() => new Address\Email(
                email: $this->address()->email(),
            ),
            str_starts_with($name, 'sms') && $this->address()->smartphone() => new Address\Phone(
                phone: $this->address()->smartphone(),
            ),
            default => null,
        };
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
        return $this->address()->locale() ?: 'en';
    }
    
    /**
     * Returns the recipient id.
     *
     * @return null|int|string
     */
    public function getId(): null|int|string
    {
        return $this->address()->userId();
    }
    
    /**
     * Returns the recipient type.
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->type() ?: $this->address()::class;
    }
}