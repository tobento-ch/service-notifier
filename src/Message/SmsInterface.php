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

use Tobento\Service\Notifier\Address\PhoneInterface;
use Tobento\Service\Notifier\ParametersInterface;
use Tobento\Service\Notifier\ParameterInterface;

/**
 * SmsInterface
 */
interface SmsInterface
{
    /**
     * Returns the subject.
     *
     * @return string
     */
    public function getSubject(): string;
    
    /**
     * Returns the from address.
     *
     * @return null|PhoneInterface
     */
    public function getFrom(): null|PhoneInterface;
    
    /**
     * Returns the address to send to.
     *
     * @return null|PhoneInterface
     */
    public function getTo(): null|PhoneInterface;
    
    /**
     * The address to send to.
     *
     * @param string|PhoneInterface $address
     * @return static $this
     */
    public function to(string|PhoneInterface $address): static;
    
    /**
     * Returns the parameters.
     *
     * @return ParametersInterface
     */
    public function parameters(): ParametersInterface;
    
    /**
     * Add a parameter.
     *
     * @param ParameterInterface $parameter
     * @return static $this
     */
    public function parameter(ParameterInterface $parameter): static;
}