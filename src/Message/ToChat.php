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
 * ToChat
 */
interface ToChat
{
    /**
     * Returns the chat handler creating the chat message.
     *
     * @return callable
     */
    public function toChatHandler(): callable;
}