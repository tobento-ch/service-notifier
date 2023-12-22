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

use Tobento\Service\Queue\JobHandlerInterface;
use Tobento\Service\Queue\JobInterface;
use Tobento\Service\Queue\JobException;
use Tobento\Service\Notifier\NotifierInterface;

/**
 * NotificationJobHandler
 */
class NotificationJobHandler implements JobHandlerInterface
{
    /**
     * Create a new NotificationJobHandler.
     *
     * @param NotifierInterface $notifier
     */
    public function __construct(
        private NotifierInterface $notifier,
    ) {}

    /**
     * Handles the specified job.
     *
     * @param JobInterface $job
     * @return void
     * @throws JobException
     */
    public function handleJob(JobInterface $job): void
    {
        $this->notifier->send(
            unserialize($job->getPayload()['notification']),
            unserialize($job->getPayload()['recipient']),
        );
    }
}