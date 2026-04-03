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
use Tobento\Service\Notifier\Message\Browser;
use Tobento\Service\Notifier\Message\BrowserInterface;
use Tobento\Service\Notifier\ParametersInterface;
use Tobento\Service\Notifier\Parameter\Queue;

class BrowserTest extends TestCase
{
    public function testThatImplementsBrowserInterface()
    {
        $this->assertInstanceof(BrowserInterface::class, new Browser([]));
    }
    
    public function testInterfaceMethods()
    {
        $msg = new Browser(data: ['key' => 'value']);
        
        $this->assertSame(['key' => 'value'], $msg->getData());
        $this->assertInstanceof(ParametersInterface::class, $msg->parameters());
        $msg->parameter(new Queue());
    }
}