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

use Tobento\Service\Mail;

/**
 * Notification
 */
class Notification extends AbstractNotification implements
    Message\ToMail,
    Message\ToSms,
    Message\ToStorage,
    Message\ToChat,
    Message\ToPush
{
    /**
     * @var string
     */
    protected string $subject = '';
    
    /**
     * @var string
     */
    protected string $content = '';
    
    /**
     * @var array<string, object> The custom messages.
     */
    protected array $messages = [];
    
    /**
     * Create a new Notification.
     *
     * @param string $subject
     * @param string $content
     * @param array<int, string> $channels The channels the recipient gets notified.
     */
    public function __construct(
        string $subject = '',
        string $content = '',
        array $channels = [],
    ) {
        $this->subject($subject);
        $this->content($content);
        $this->channels = $channels;
    }

    /**
     * Set the subject.
     *
     * @param string $subject
     * @return static $this
     */
    public function subject(string $subject): static
    {
        $this->subject = $subject;
        return $this;
    }
        
    /**
     * Returns the subject.
     *
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }
    
    /**
     * Set the content.
     *
     * @param string $content
     * @return static $this
     */
    public function content(string $content): static
    {
        $this->content = $content;
        return $this;
    }
        
    /**
     * Returns the content.
     *
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }
    
    /**
     * Add a message for the specified channel.
     *
     * @param string $channel The channel name
     * @param object $message
     * @return static $this
     */
    public function addMessage(string $channel, object $message): static
    {
        $this->messages[$channel] = $message;
        return $this;
    }
    
    /**
     * Returns the mail message.
     *
     * @param RecipientInterface $recipient
     * @param string $channel The channel name.
     * @return Mail\MessageInterface
     */
    public function toMail(RecipientInterface $recipient, string $channel): Mail\MessageInterface
    {
        $message = $this->getMessageFor(channel: $channel, channelRoot: 'mail');
        
        if ($message instanceof Mail\MessageInterface) {
            return $message;
        }
        
        return (new Mail\Message())
            ->subject($this->subject)
            ->text($this->content);
    }
    
    /**
     * Returns the sms message.
     *
     * @param RecipientInterface $recipient
     * @param string $channel The channel name.
     * @return Message\SmsInterface
     */
    public function toSms(RecipientInterface $recipient, string $channel): Message\SmsInterface
    {
        $message = $this->getMessageFor(channel: $channel, channelRoot: 'sms');
        
        if ($message instanceof Message\SmsInterface) {
            return $message;
        }

        return new Message\Sms(
            subject: $this->subject,
        );
    }
    
    /**
     * Returns the storage message.
     *
     * @param RecipientInterface $recipient
     * @param string $channel The channel name.
     * @return Message\StorageInterface
     */
    public function toStorage(RecipientInterface $recipient, string $channel): Message\StorageInterface
    {
        $message = $this->getMessageFor(channel: $channel, channelRoot: 'storage');
        
        if ($message instanceof Message\StorageInterface) {
            return $message;
        }

        return new Message\Storage(data: [
            'subject' => $this->subject,
            'content' => $this->content,
        ]);
    }
    
    /**
     * Returns the chat message.
     *
     * @param RecipientInterface $recipient
     * @param string $channel The channel name.
     * @return Message\ChatInterface
     */
    public function toChat(RecipientInterface $recipient, string $channel): Message\ChatInterface
    {
        $message = $this->getMessageFor(channel: $channel, channelRoot: 'chat');
        
        if ($message instanceof Message\ChatInterface) {
            return $message;
        }
        
        return new Message\Chat(
            subject: $this->subject,
        );
    }
    
    /**
     * Returns the push message.
     *
     * @param RecipientInterface $recipient
     * @param string $channel The channel name.
     * @return Message\PushInterface
     */
    public function toPush(RecipientInterface $recipient, string $channel): Message\PushInterface
    {
        $message = $this->getMessageFor(channel: $channel, channelRoot: 'push');
        
        if ($message instanceof Message\PushInterface) {
            return $message;
        }
        
        return new Message\Push(
            subject: $this->subject,
            content: $this->content,
        );
    }
    
    /**
     * Returns the message for the channel or null if none exists.
     *
     * @param string $channel The channel name such as 'mail', mail/mailchimp'.
     * @param string $channelRoot The channel root name such as 'mail'.
     * @return null|object
     */
    protected function getMessageFor(string $channel, string $channelRoot): null|object
    {
        if (isset($this->messages[$channel])) {
            return $this->messages[$channel];
        }
        
        foreach($this->messages as $channelName => $message) {
            if (str_starts_with($channelName, $channelRoot)) {
                return $message;
            }
        }
        
        return null;
    }
}