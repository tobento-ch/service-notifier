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
use Tobento\Service\Notifier\Message\Chat;
use Tobento\Service\Notifier\Message\ChatInterface;
use Tobento\Service\Notifier\ParametersInterface;
use Tobento\Service\Notifier\Parameter\Queue;

class ChatTest extends TestCase
{
    public function testThatImplementsChatInterface()
    {
        $this->assertInstanceof(ChatInterface::class, new Chat());
    }
    
    public function testInterfaceMethods()
    {
        $msg = new Chat(subject: 'Subject');
        
        $this->assertSame('Subject', $msg->getSubject());
        $this->assertInstanceof(ParametersInterface::class, $msg->parameters());
        $msg->parameter(new Queue());
    }
}