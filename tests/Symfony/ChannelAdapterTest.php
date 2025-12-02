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

namespace Tobento\Service\Notifier\Test\Symfony;

use PHPUnit\Framework\TestCase;
use Tobento\Service\Notifier\Address;
use Tobento\Service\Notifier\Symfony\ChannelAdapter;
use Tobento\Service\Notifier\Symfony\MessageOptions;
use Tobento\Service\Notifier\Symfony\PerRecipientChatChannel;
use Tobento\Service\Notifier\ChannelInterface;
use Tobento\Service\Notifier\Notification;
use Tobento\Service\Notifier\Recipient;
use Tobento\Service\Notifier\Message;
use Tobento\Service\Notifier\Exception\InvalidAddressException;
use Tobento\Service\Notifier\Exception\UndefinedAddressException;
use Symfony\Component\Notifier\Channel\SmsChannel;
use Symfony\Component\Notifier\Channel\ChatChannel;
use Symfony\Component\Notifier\Channel\PushChannel;
use Symfony\Component\Notifier\Message\SmsMessage;
use Symfony\Component\Notifier\Message\ChatMessage;
use Symfony\Component\Notifier\Message\PushMessage;
use Symfony\Component\Notifier\Transport\NullTransport;
use Symfony\Component\Notifier\Bridge\Slack\SlackOptions;
use Symfony\Component\Notifier\Bridge\OneSignal\OneSignalOptions;
use Tobento\Service\Container\Container;

class ChannelAdapterTest extends TestCase
{
    public function testCreateAdapter()
    {
        $channel = new SmsChannel(
            transport: new NullTransport(),
        );
        
        $adapter = new ChannelAdapter(
            name: 'sms/vonage',
            channel: $channel,
            container: new Container(),
        );
        
        $this->assertInstanceof(ChannelInterface::class, $adapter);
        $this->assertSame('sms/vonage', $adapter->name());
        $this->assertSame($channel, $adapter->channel());
    }
    
    public function testSendSms()
    {
        $adapter = new ChannelAdapter(
            name: 'sms/vonage',
            channel: new SmsChannel(transport: new NullTransport()),
            container: new Container(),
        );
        
        $msg = $adapter->send(
            notification: new Notification('Subject'),
            recipient: new Recipient(phone: '15556666666'),
        );
        
        $this->assertInstanceof(SmsMessage::class, $msg);
        $this->assertSame('Subject', $msg->getSubject());
        $this->assertSame('15556666666', $msg->getPhone());
        $this->assertSame('', $msg->getFrom());
    }
    
    public function testSendSmsWithCustomMessage()
    {
        $adapter = new ChannelAdapter(
            name: 'sms',
            channel: new SmsChannel(transport: new NullTransport()),
            container: new Container(),
        );
        
        $message = new Message\Sms(
            subject: 'Custom message',
            from: 'FROM',
        );
        
        $msg = $adapter->send(
            notification: new Notification('Subject')->addMessage('sms', $message),
            recipient: new Recipient(phone: '15556666666'),
        );
        
        $this->assertInstanceof(SmsMessage::class, $msg);
        $this->assertSame('Custom message', $msg->getSubject());
        $this->assertSame('15556666666', $msg->getPhone());
        $this->assertSame('FROM', $msg->getFrom());
    }
    
    public function testSendSmsThrowsUndefinedAddressExceptionIfNoAddress()
    {
        $this->expectException(UndefinedAddressException::class);
        
        $adapter = new ChannelAdapter(
            name: 'sms/vonage',
            channel: new SmsChannel(transport: new NullTransport()),
            container: new Container(),
        );
        
        $msg = $adapter->send(
            notification: new Notification('Subject'),
            recipient: new Recipient(),
        );
    }
    
    public function testSendChat()
    {
        $adapter = new ChannelAdapter(
            name: 'chat',
            channel: new ChatChannel(transport: new NullTransport()),
            container: new Container(),
        );
        
        $msg = $adapter->send(
            notification: new Notification('Subject'),
            recipient: new Recipient(),
        );
        
        $this->assertInstanceof(ChatMessage::class, $msg);
        $this->assertSame('Subject', $msg->getSubject());
    }
    
    public function testSendChatWithOptions()
    {
        $adapter = new ChannelAdapter(
            name: 'chat/slack',
            channel: new ChatChannel(transport: new NullTransport()),
            container: new Container(),
        );
        
        $options = new SlackOptions([
            'recipient_id' => 'channel',
        ]);
        
        $message = new Message\Chat(
            subject: 'Chat message',
        )->parameter(new MessageOptions($options));
        
        $msg = $adapter->send(
            notification: (new Notification('Subject'))->addMessage('chat/slack', $message),
            recipient: new Recipient(),
        );
        
        $this->assertInstanceof(ChatMessage::class, $msg);
        $this->assertSame('Chat message', $msg->getSubject());
        $this->assertSame($options, $msg->getOptions());
    }
    
    public function testSendChatPerRecipient()
    {
        $adapter = new ChannelAdapter(
            name: 'chat',
            channel: new PerRecipientChatChannel(),
            container: new Container(),
        );
        
        $message = new Message\Chat(subject: 'Chat message');
        
        $msg = $adapter->send(
            notification: new Notification('Subject')->addMessage('chat', $message),
            recipient: new Recipient()->addAddress(
                channel: 'chat',
                address: new Address\Dsn('null://null'),
            ),
        );
        
        $this->assertInstanceof(ChatMessage::class, $msg);
        $this->assertSame('Chat message', $msg->getSubject());
    }

    public function testSendChatPerRecipientWillThrowUndefinedAddressExceptionIfNoAddress()
    {
        $this->expectException(UndefinedAddressException::class);
        $this->expectExceptionMessage('Notification Tobento\Service\Notifier\Notification has no address for the channel chat defined');
        
        $adapter = new ChannelAdapter(
            name: 'chat',
            channel: new PerRecipientChatChannel(),
            container: new Container(),
        );
        
        $message = new Message\Chat(subject: 'Chat message');
        
        $msg = $adapter->send(
            notification: new Notification('Subject')->addMessage('chat', $message),
            recipient: new Recipient(),
        );
        
        $this->assertInstanceof(ChatMessage::class, $msg);
        $this->assertSame('Chat message', $msg->getSubject());
    }
    
    public function testSendChatPerRecipientWillThrowInvalidAddressExceptionIfInvalid()
    {
        $this->expectException(InvalidAddressException::class);
        $this->expectExceptionMessage('Notification Tobento\Service\Notifier\Notification has invalid address for the channel chat defined');
        
        $adapter = new ChannelAdapter(
            name: 'chat',
            channel: new PerRecipientChatChannel(),
            container: new Container(),
        );
        
        $message = new Message\Chat(subject: 'Chat message');
        
        $msg = $adapter->send(
            notification: new Notification('Subject')->addMessage('chat', $message),
            recipient: new Recipient()->addAddress(
                channel: 'chat',
                address: new Address\Dsn('slack://TOKEN@default?channel=CHANNEL'),
            ),
        );
        
        $this->assertInstanceof(ChatMessage::class, $msg);
        $this->assertSame('Chat message', $msg->getSubject());
    }
    
    public function testSendPush()
    {
        $adapter = new ChannelAdapter(
            name: 'push',
            channel: new PushChannel(transport: new NullTransport()),
            container: new Container(),
        );
        
        $msg = $adapter->send(
            notification: new Notification('Subject'),
            recipient: new Recipient(),
        );
        
        $this->assertInstanceof(PushMessage::class, $msg);
        $this->assertSame('Subject', $msg->getSubject());
    }
    
    public function testSendPushWithOptions()
    {
        $adapter = new ChannelAdapter(
            name: 'push/one-signal',
            channel: new PushChannel(transport: new NullTransport()),
            container: new Container(),
        );
        
        $options = new OneSignalOptions([]);
        
        $message = (new Message\Push(
            subject: 'Push message',
        ))->parameter(new MessageOptions($options));
        
        $msg = $adapter->send(
            notification: (new Notification('Subject'))->addMessage('push/one-signal', $message),
            recipient: new Recipient(),
        );
        
        $this->assertInstanceof(PushMessage::class, $msg);
        $this->assertSame('Push message', $msg->getSubject());
        $this->assertSame($options, $msg->getOptions());
    }
}