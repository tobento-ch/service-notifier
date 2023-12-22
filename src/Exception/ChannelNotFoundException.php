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

namespace Tobento\Service\Notifier\Exception;

use Throwable;

/**
 * ChannelNotFoundException
 */
class ChannelNotFoundException extends ChannelException
{
    /**
     * Create a new ChannelNotFoundException.
     *
     * @param string $name The channel name
     * @param string $message The message
     * @param int $code
     * @param null|Throwable $previous
     */
    public function __construct(
        protected string $name,
        string $message = '',
        int $code = 0,
        null|Throwable $previous = null
    ) {
        if (empty($message)) {
            $message = sprintf('The "%s" channel does not exist.', $name);
        }
        
        parent::__construct($message, $code, $previous);
    }
    
    /**
     * Returns the name.
     *
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }
}