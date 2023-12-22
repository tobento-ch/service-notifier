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
 * Phone
 */
class Phone implements PhoneInterface
{
    /**
     * Create a new Phone.
     *
     * @param string $phone
     * @param null|string $name
     */
    public function __construct(
        protected string $phone,
        protected null|string $name = null
    ) {}
    
    /**
     * Returns the phone.
     *
     * @return string
     */
    public function phone(): string
    {
        return $this->phone;
    }
    
    /**
     * Returns the name.
     *
     * @return null|string
     */
    public function name(): null|string
    {
        return $this->name;
    }
}