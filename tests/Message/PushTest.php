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
use Tobento\Service\Notifier\Message\Push;
use Tobento\Service\Notifier\Message\PushInterface;
use Tobento\Service\Notifier\ParametersInterface;
use Tobento\Service\Notifier\Parameter\Queue;

class PushTest extends TestCase
{
    public function testThatImplementsPushInterface()
    {
        $this->assertInstanceof(PushInterface::class, new Push());
    }
    
    public function testInterfaceMethods()
    {
        $msg = new Push(subject: 'Subject', content: 'Content');
        
        $this->assertSame('Subject', $msg->getSubject());
        $this->assertSame('Content', $msg->getContent());
        $this->assertInstanceof(ParametersInterface::class, $msg->parameters());
        $msg->parameter(new Queue());
    }
}