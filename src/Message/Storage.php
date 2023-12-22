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
 * Storage message.
 */
class Storage implements StorageInterface
{
    use HasParameters;
    
    /**
     * Create a new Storage.
     *
     * @param array $data
     */
    public function __construct(
        protected array $data,
    ) {}
    
    /**
     * Returns the data.
     *
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }
}