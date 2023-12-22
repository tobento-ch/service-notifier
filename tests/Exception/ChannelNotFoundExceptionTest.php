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
use Tobento\Service\Notifier\Exception\ChannelNotFoundException;
use Tobento\Service\Notifier\Exception\ChannelException;

class ChannelNotFoundExceptionTest extends TestCase
{
    public function testException()
    {
        $e = new ChannelNotFoundException(name: 'channel');
        
        $this->assertInstanceof(ChannelException::class, $e);
        $this->assertSame('channel', $e->name());
        $this->assertSame('The "channel" channel does not exist.', $e->getMessage());
        
        $this->assertSame(
            'Custom',
            (new ChannelNotFoundException(name: 'channel', message: 'Custom'))->getMessage()
        );
    }
}