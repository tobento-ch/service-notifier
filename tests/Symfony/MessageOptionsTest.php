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

namespace Tobento\Service\Notifier\Test\Symfony;

use PHPUnit\Framework\TestCase;
use Tobento\Service\Notifier\Symfony\MessageOptions;
use Tobento\Service\Notifier\ParameterInterface;
use Symfony\Component\Notifier\Bridge\Slack\SlackOptions;

class MessageOptionsTest extends TestCase
{
    public function testInterfaceMethods()
    {
        $param = new MessageOptions(options: new SlackOptions());
        
        $this->assertInstanceof(ParameterInterface::class, $param);
        $this->assertSame(MessageOptions::class, $param->getName());
        $this->assertSame(0, $param->getPriority());
    }
    
    public function testSpecificMethods()
    {
        $options = new SlackOptions();
        $param = new MessageOptions($options);

        $this->assertSame($options, $param->getOptions());
    }
}