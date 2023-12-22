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
 * Email
 */
class Email implements EmailInterface
{
    /**
     * Create a new Email.
     *
     * @param string $email
     * @param null|string $name
     */
    public function __construct(
        protected string $email,
        protected null|string $name = null
    ) {}
    
    /**
     * Returns the email.
     *
     * @return string
     */
    public function email(): string
    {
        return $this->email;
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