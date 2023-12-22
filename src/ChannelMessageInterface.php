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

use Throwable;

/**
 * ChannelMessageInterface
 */
interface ChannelMessageInterface
{
    /**
     * Returns the channel name.
     *
     * @return string
     */
    public function channel(): string;
    
    /**
     * Returns the message.
     *
     * @return null|object
     */
    public function message(): null|object;
    
    /**
     * Returns the exception.
     *
     * @return null|Throwable
     */
    public function exception(): null|Throwable;
    
    /**
     * Returns true if the message was successful, otherwise false.
     *
     * @return bool
     */
    public function isSuccessful(): bool;
    
    /**
     * Returns true if the message failed, otherwise false.
     *
     * @return bool
     */
    public function isFailure(): bool;
}