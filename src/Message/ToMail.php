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

/**
 * ToMail
 */
interface ToMail
{
    /**
     * Returns the mail handler creating the mail message.
     *
     * @return callable
     */
    public function toMailHandler(): callable;
}