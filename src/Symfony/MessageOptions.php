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

namespace Tobento\Service\Notifier\Symfony;

use Tobento\Service\Notifier\Parameter\Parameter;
use Symfony\Component\Notifier\Message\MessageOptionsInterface;

/**
 * MessageOptions
 */
class MessageOptions extends Parameter
{
    /**
     * Create a new MessageOptions.
     *
     * @param MessageOptionsInterface $options
     */
    public function __construct(
        protected MessageOptionsInterface $options,
    ) {}
    
    public function getOptions(): MessageOptionsInterface
    {
        return $this->options;
    }
}