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

namespace Tobento\Service\Notifier\Address;

/**
 * PhoneInterface
 */
interface PhoneInterface
{
    /**
     * Returns the phone.
     *
     * @return string
     */
    public function phone(): string;
    
    /**
     * Returns the name.
     *
     * @return null|string
     */
    public function name(): null|string;
}