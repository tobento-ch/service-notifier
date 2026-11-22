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

/**
 * NotificationAdapterInterface, e.g. for a notification replacing another one.
 */
interface NotificationAdapterInterface
{
    /**
     * Returns the adapted notification.
     *
     * @return NotificationInterface
     */
    public function adaptedNotification(): NotificationInterface;
}