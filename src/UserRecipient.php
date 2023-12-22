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
use Tobento\Service\User\UserInterface;

/**
 * UserRecipient
 */
class UserRecipient implements RecipientInterface
{
    /**
     * Create a new UserRecipient.
     *
     * @param UserInterface $user
     * @param array $channels
     */
    public function __construct(
        protected UserInterface $user,
        protected array $channels = [],
    ) {}
    
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
            str_starts_with($name, 'mail') && $this->user->email() => new Address\Email(
                email: $this->user->email(),
            ),
            str_starts_with($name, 'sms') && $this->user->smartphone() => new Address\Phone(
                phone: $this->user->smartphone(),
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
        return $this->user->locale() ?: 'en';
    }
    
    /**
     * Returns the recipient id.
     *
     * @return null|int|string
     */
    public function getId(): null|int|string
    {
        return $this->user->id();
    }
    
    /**
     * Returns the recipient type.
     *
     * @return string
     */
    public function getType(): string
    {
        return $this->user::class;
    }
}