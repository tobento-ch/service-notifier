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

namespace Tobento\Service\Notifier\Queue;

use Tobento\Service\Notifier\QueueHandlerInterface;
use Tobento\Service\Notifier\NotificationInterface;
use Tobento\Service\Notifier\RecipientInterface;
use Tobento\Service\Notifier\Parameter;
use Tobento\Service\Queue\QueueInterface;
use Tobento\Service\Queue\Job;

/**
 * QueueHandler
 */
class QueueHandler implements QueueHandlerInterface
{
    /**
     * Create a new QueueHandler.
     *
     * @param QueueInterface $queue
     * @param null|string $queueName The default queue used if no specific is defined on the message.
     */
    public function __construct(
        protected QueueInterface $queue,
        protected null|string $queueName = null,
    ) {}
    
    /**
     * Handle the notification.
     *
     * @param NotificationInterface $notification
     * @param RecipientInterface $recipient
     * @return void
     */
    public function handle(NotificationInterface $notification, RecipientInterface $recipient): void
    {
        $queue = $notification->parameters()->name(Parameter\Queue::class)->first();
        $notification->parameters()->remove(Parameter\Queue::class);
        
        $job = new Job(
            name: NotificationJobHandler::class,
            payload: [
                'notification' => serialize($notification),
                'recipient' => serialize($recipient),
            ],
        );
        
        if ($queue instanceof Parameter\Queue) {
            
            if ($queue->name()) {
                $job->queue($queue->name());
            } elseif ($this->queueName) {
                $job->queue($this->queueName);
            }
            
            if ($queue->delay() > 0) {
                $job->delay($queue->delay());
            }
            
            $job->retry($queue->retry());
            $job->priority($queue->priority());
            
            if ($queue->encrypt()) {
                $job->encrypt();
            }
        }
        
        $this->queue->push($job);
    }
}