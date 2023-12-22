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

namespace Tobento\Service\Notifier\Test\Queue;

use PHPUnit\Framework\TestCase;
use Tobento\Service\Notifier\Queue\QueueHandler;
use Tobento\Service\Notifier\Queue\NotificationJobHandler;
use Tobento\Service\Notifier\QueueHandlerInterface;
use Tobento\Service\Notifier\Notification;
use Tobento\Service\Notifier\Recipient;
use Tobento\Service\Notifier\Parameter;
use Tobento\Service\Queue\Queues;
use Tobento\Service\Queue\InMemoryQueue;
use Tobento\Service\Queue\JobProcessor;
use Tobento\Service\Queue\Parameter as Param;
use Tobento\Service\Container\Container;

class QueueHandlerTest extends TestCase
{
    public function testHandleMethod()
    {
        $queue = new Queues(
            new InMemoryQueue(
                name: 'inmemory',
                jobProcessor: new JobProcessor(new Container()),
            ),
        );
        
        $handler = new QueueHandler(queue: $queue);
                
        $notification = (new Notification('Subject'))
            ->parameter(new Parameter\Queue());
        
        $recipient = new Recipient();
        
        $handler->handle(
            notification: $notification,
            recipient: $recipient,
        );
        
        $job = $queue->pop();
        
        $this->assertInstanceof(QueueHandlerInterface::class, $handler);
        $this->assertSame(NotificationJobHandler::class, $job->getName());
        $this->assertArrayHasKey('notification', $job->getPayload());
        $this->assertArrayHasKey('recipient', $job->getPayload());
        $this->assertSame('inmemory', $job->parameters()->get(Param\Queue::class)?->name());
        $this->assertSame(null, $job->parameters()->get(Param\Delay::class)?->seconds());
        $this->assertSame(3, $job->parameters()->get(Param\Retry::class)?->max());
        $this->assertSame(0, $job->parameters()->get(Param\Priority::class)?->priority());
        $this->assertSame(null, $job->parameters()->get(Param\Encrypt::class));
    }
    
    public function testHandleMethodWithSpecificQueueValues()
    {
        $queue = new Queues(
            new InMemoryQueue(
                name: 'inmemory',
                jobProcessor: new JobProcessor(new Container()),
            ),
            new InMemoryQueue(
                name: 'foo',
                jobProcessor: new JobProcessor(new Container()),
            ),
        );
        
        $handler = new QueueHandler(queue: $queue);
                
        $notification = (new Notification('Subject'))
            ->parameter(new Parameter\Queue(
                name: 'foo',
                delay: 30,
                retry: 5,
                priority: 100,
                encrypt: false,
            ));
        
        $recipient = new Recipient();
        
        $handler->handle(
            notification: $notification,
            recipient: $recipient,
        );
        
        $job = $queue->pop();

        $this->assertSame('foo', $job->parameters()->get(Param\Queue::class)?->name());
        $this->assertSame(30, $job->parameters()->get(Param\Delay::class)?->seconds());
        $this->assertSame(5, $job->parameters()->get(Param\Retry::class)?->max());
        $this->assertSame(100, $job->parameters()->get(Param\Priority::class)?->priority());
        $this->assertSame(null, $job->parameters()->get(Param\Encrypt::class));
    }
    
    public function testHandleMethodUsesQueueNameIfNoSpecificSet()
    {
        $queue = new Queues(
            new InMemoryQueue(
                name: 'inmemory',
                jobProcessor: new JobProcessor(new Container()),
            ),
            new InMemoryQueue(
                name: 'foo',
                jobProcessor: new JobProcessor(new Container()),
            ),
            new InMemoryQueue(
                name: 'bar',
                jobProcessor: new JobProcessor(new Container()),
            ),
        );
        
        $handler = new QueueHandler(queue: $queue, queueName: 'bar');
                
        $notification = (new Notification('Subject'))
            ->parameter(new Parameter\Queue());
        
        $recipient = new Recipient();
        
        $handler->handle(
            notification: $notification,
            recipient: $recipient,
        );
        
        $job = $queue->pop();

        $this->assertSame('bar', $job->parameters()->get(Param\Queue::class)?->name());
        
        // should use specific:
        $notification = (new Notification('Subject'))
            ->parameter(new Parameter\Queue(name: 'foo'));
        
        $handler->handle(
            notification: $notification,
            recipient: $recipient,
        );
        
        $job = $queue->pop();

        $this->assertSame('foo', $job->parameters()->get(Param\Queue::class)?->name());
    }
}