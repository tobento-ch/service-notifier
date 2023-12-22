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

/**
 * NotificationInterface
 */
interface NotificationInterface
{
    /**
     * Returns a notification name.
     *
     * @return string
     */
    public function getName(): string;
    
    /**
     * Returns the channels the recipient gets notified.
     *
     * @param RecipientInterface $recipient
     * @return array<int, string> ['mail', 'sms']
     */
    public function getChannels(RecipientInterface $recipient): array;
    
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