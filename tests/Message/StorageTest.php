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

namespace Tobento\Service\Notifier\Test\Message;

use PHPUnit\Framework\TestCase;
use Tobento\Service\Notifier\Message\Storage;
use Tobento\Service\Notifier\Message\StorageInterface;
use Tobento\Service\Notifier\ParametersInterface;
use Tobento\Service\Notifier\Parameter\Queue;

class StorageTest extends TestCase
{
    public function testThatImplementsStorageInterface()
    {
        $this->assertInstanceof(StorageInterface::class, new Storage([]));
    }
    
    public function testInterfaceMethods()
    {
        $msg = new Storage(data: ['key' => 'value']);
        
        $this->assertSame(['key' => 'value'], $msg->getData());
        $this->assertInstanceof(ParametersInterface::class, $msg->parameters());
        $msg->parameter(new Queue());
    }
}