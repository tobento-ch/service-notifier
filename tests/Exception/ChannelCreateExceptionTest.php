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

namespace Tobento\Service\Notifier\Test\Exception;

use PHPUnit\Framework\TestCase;
use Tobento\Service\Notifier\Exception\ChannelCreateException;
use Tobento\Service\Notifier\Exception\ChannelException;

class ChannelCreateExceptionTest extends TestCase
{
    public function testException()
    {
        $e = new ChannelCreateException(name: 'channel');
        
        $this->assertInstanceof(ChannelException::class, $e);
        $this->assertSame('channel', $e->name());
    }
}