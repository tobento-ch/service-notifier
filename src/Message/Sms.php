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

use Tobento\Service\Notifier\HasParameters;
use Tobento\Service\Notifier\Address\PhoneInterface;
use Tobento\Service\Notifier\Address\Phone;

/**
 * Sms
 */
class Sms implements SmsInterface
{
    use HasParameters;
    
    /**
     * @var null|PhoneInterface
     */
    protected null|PhoneInterface $from = null;
    
    /**
     * @var null|PhoneInterface
     */
    protected null|PhoneInterface $to = null;
    
    /**
     * Create a new Sms.
     *
     * @param string $subject
     * @param null|string|PhoneInterface $from
     * @param null|string|PhoneInterface $to
     */
    public function __construct(
        protected string $subject = '',
        null|string|PhoneInterface $from = null,
        null|string|PhoneInterface $to = null,
    ) {
        if (!is_null($from)) {
            $this->from($from);
        }
        
        if (!is_null($to)) {
            $this->to($to);
        }
    }
    
    /**
     * Set the subject.
     *
     * @param string $subject
     * @return static $this
     */
    public function subject(string $subject): static
    {
        $this->subject = $subject;
        
        return $this;
    }
    
    /**
     * Returns the subject.
     *
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }
    
    /**
     * Set the from address.
     *
     * @param string|PhoneInterface $address
     * @return static $this
     */
    public function from(string|PhoneInterface $address): static
    {
        $this->from = is_string($address) ? new Phone(phone: $address) : $address;
        
        return $this;
    }
    
    /**
     * Returns the from address.
     *
     * @return null|PhoneInterface
     */
    public function getFrom(): null|PhoneInterface
    {
        return $this->from;
    }
    
    /**
     * The address to send to.
     *
     * @param string|PhoneInterface $address
     * @return static $this
     */
    public function to(string|PhoneInterface $address): static
    {
        $this->to = is_string($address) ? new Phone(phone: $address) : $address;
        
        return $this;
    }
    
    /**
     * Returns the address to send to.
     *
     * @return null|PhoneInterface
     */
    public function getTo(): null|PhoneInterface
    {
        return $this->to;
    }
}