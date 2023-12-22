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
 * Push
 */
class Push implements PushInterface
{
    use HasParameters;
    
    /**
     * Create a new Push.
     *
     * @param string $subject
     * @param string $content
     */
    public function __construct(
        protected string $subject = '',
        protected string $content = '',
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
    
    /**
     * Set the content.
     *
     * @param string $content
     * @return static $this
     */
    public function content(string $content): static
    {
        $this->content = $content;
        return $this;
    }
    
    /**
     * Returns the content.
     *
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }
}