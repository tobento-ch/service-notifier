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

/**
 * Chat
 */
class Chat implements ChatInterface
{
    use HasParameters;
    
    /**
     * Create a new Chat.
     *
     * @param string $subject
     */
    public function __construct(
        protected string $subject = '',
    ) {}
    
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
}