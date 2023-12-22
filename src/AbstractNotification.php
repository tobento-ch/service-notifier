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
use Tobento\Service\Notifier\Parameter;
use Tobento\Service\Notifier\Address;

/**
 * AbstractNotification
 */
abstract class AbstractNotification implements NotificationInterface
{
    use HasParameters;
    use InteractsWithParameters;
    
    /**
     * @var string
     */
    protected string $name = '';
    
    /**
     * @var array<int, string> The channels the recipient gets notified.
     */
    protected array $channels = [];
    
    /**
     * Returns a notification name.
     *
     * @return string
     */
    public function getName(): string
    {
        return !empty($this->name) ? $this->name : static::class;
    }
    
    /**
     * Set the notification name.
     *
     * @param string $name
     * @return static $this
     */
    public function name(string $name): static
    {
        $this->name = $name;
        return $this;
    }
    
    /**
     * Returns the channels the recipient gets notified.
     *
     * @param RecipientInterface $recipient
     * @return array<int, string> ['mail', 'sms']
     */
    public function getChannels(RecipientInterface $recipient): array
    {
        $channels = $recipient->getChannels($this);
        
        return !empty($channels) ? $channels : $this->channels;
    }
    
    /**
     * Set the channels the recipient gets notified.
     *
     * @param array<int, string> $channels
     * @return static $this
     */
    public function channels(array $channels): static
    {
        $this->channels = $channels;
        return $this;
    }
    
    /**
     * Returns the mail handler creating the mail message.
     *
     * @return callable
     * @psalm-suppress all
     */
    public function toMailHandler(): callable
    {
        return [$this, 'toMail'];
    }
    
    /**
     * Returns the sms handler creating the sms message.
     *
     * @return callable
     * @psalm-suppress all
     */
    public function toSmsHandler(): callable
    {
        return [$this, 'toSms'];
    }
    
    /**
     * Returns the storage handler creating the storage message.
     *
     * @return callable
     * @psalm-suppress all
     */
    public function toStorageHandler(): callable
    {
        return [$this, 'toStorage'];
    }
    
    /**
     * Returns the chat handler creating the chat message.
     *
     * @return callable
     * @psalm-suppress all
     */
    public function toChatHandler(): callable
    {
        return [$this, 'toChat'];
    }
    
    /**
     * Returns the push handler creating the push message.
     *
     * @return callable
     * @psalm-suppress all
     */
    public function toPushHandler(): callable
    {
        return [$this, 'toPush'];
    }
}