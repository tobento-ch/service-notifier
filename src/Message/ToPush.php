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

namespace Tobento\Service\Notifier\Message;

use Tobento\Service\Notifier\RecipientInterface;

/**
 * ToPush
 */
interface ToPush
{
    /**
     * Returns the push handler creating the push message.
     *
     * @return callable
     */
    public function toPushHandler(): callable;
}