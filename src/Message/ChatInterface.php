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

use Tobento\Service\Notifier\ParametersInterface;
use Tobento\Service\Notifier\ParameterInterface;

/**
 * ChatInterface
 */
interface ChatInterface
{
    /**
     * Returns the subject.
     *
     * @return string
     */
    public function getSubject(): string;
    
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