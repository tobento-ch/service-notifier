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

namespace Tobento\Service\Notifier\Test;

use PHPUnit\Framework\TestCase;
use Tobento\Service\Notifier\ChannelMessages;
use Tobento\Service\Notifier\ChannelMessagesInterface;
use Tobento\Service\Notifier\ChannelMessage;
use Tobento\Service\Notifier\ChannelMessageInterface;
use Tobento\Service\Notifier\Notification;
use Tobento\Service\Notifier\Recipient;

class ChannelMessagesTest extends TestCase
{
    public function testThatImplementsChannelMessagesInterface()
    {
        $notification = new Notification('Subject');
        $recipient = new Recipient();
        $messages = new ChannelMessages(recipient: $recipient, notification: $notification);
        
        $this->assertInstanceof(ChannelMessagesInterface::class, $messages);
    }
    
    public function testRecipientAndNotificationMethods()
    {
        $notification = new Notification('Subject');
        $recipient = new Recipient();
        $messages = new ChannelMessages(recipient: $recipient, notification: $notification);

        $this->assertSame($recipient, $messages->recipient());
        $this->assertSame($notification, $messages->notification());
    }
    
    public function testAddHasAndGetMethods()
    {
        $notification = new Notification('Subject');
        $recipient = new Recipient();
        $messages = new ChannelMessages(recipient: $recipient, notification: $notification);
        $msg = new ChannelMessage(channel: 'sms');
        $messages->add($msg);
        
        $this->assertTrue($messages->has('sms'));
        $this->assertFalse($messages->has('mail'));
        $this->assertSame($msg, $messages->get('sms'));
        $this->assertSame(null, $messages->get('mail'));
    }
    
    public function testAllMethod()
    {
        $notification = new Notification('Subject');
        $recipient = new Recipient();
        $messages = new ChannelMessages(recipient: $recipient, notification: $notification);
        $msg = new ChannelMessage(channel: 'sms');
        
        $this->assertSame([], $messages->all());
        
        $messages->add($msg);
        
        $this->assertSame(['sms' => $msg], $messages->all());
    }
    
    public function testSuccessfulMethod()
    {
        $notification = new Notification('Subject');
        $recipient = new Recipient();
        $messages = new ChannelMessages(recipient: $recipient, notification: $notification);

        $messages->add(new ChannelMessage(channel: 'sms'));
        $messages->add(new ChannelMessage(channel: 'mail', exception: new \Exception()));
        
        $messagesNew = $messages->successful();
        
        $this->assertNotSame($messages, $messagesNew);
        $this->assertSame(2, $messages->count());
        $this->assertSame(1, $messagesNew->count());
    }
    
    public function testFailedMethod()
    {
        $notification = new Notification('Subject');
        $recipient = new Recipient();
        $messages = new ChannelMessages(recipient: $recipient, notification: $notification);

        $messages->add(new ChannelMessage(channel: 'sms'));
        $messages->add(new ChannelMessage(channel: 'mail', exception: new \Exception()));
        
        $messagesNew = $messages->failed();
        
        $this->assertNotSame($messages, $messagesNew);
        $this->assertSame(2, $messages->count());
        $this->assertSame(1, $messagesNew->count());
    }
    
    public function testChannelNamesMethod()
    {
        $notification = new Notification('Subject');
        $recipient = new Recipient();
        $messages = new ChannelMessages(recipient: $recipient, notification: $notification);
        
        $this->assertSame([], $messages->channelNames());
        
        $messages->add(new ChannelMessage(channel: 'sms'));
        $messages->add(new ChannelMessage(channel: 'mail', exception: new \Exception()));
        
        $this->assertSame(['sms', 'mail'], $messages->channelNames());
    }
    
    public function testFilterMethod()
    {
        $notification = new Notification('Subject');
        $recipient = new Recipient();
        $messages = new ChannelMessages(recipient: $recipient, notification: $notification);
        $messages->add(new ChannelMessage(channel: 'sms'));
        $messages->add(new ChannelMessage(channel: 'mail', exception: new \Exception()));
        
        $messagesNew = $messages->filter(fn(ChannelMessageInterface $m): bool => $m->isFailure());
        
        $this->assertNotSame($messages, $messagesNew);
    }
    
    public function testGetIteratorMethod()
    {
        $notification = new Notification('Subject');
        $recipient = new Recipient();
        $messages = new ChannelMessages(recipient: $recipient, notification: $notification);
        $msg = new ChannelMessage(channel: 'sms');
        $messages->add($msg);
        
        $iterated = [];
        
        foreach($messages->getIterator() as $k => $m) {
            $iterated[$k] = $m;
        }
        
        $this->assertSame(['sms' => $msg], $iterated);
    }
}