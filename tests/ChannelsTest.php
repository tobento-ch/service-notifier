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

namespace Tobento\Service\Notifier\Test;

use PHPUnit\Framework\TestCase;
use Tobento\Service\Notifier\Channels;
use Tobento\Service\Notifier\ChannelsInterface;
use Tobento\Service\Notifier\NullChannel;
use Tobento\Service\Notifier\Exception\ChannelNotFoundException;

class ChannelsTest extends TestCase
{
    public function testThatImplementsChannelsInterface()
    {
        $this->assertInstanceof(ChannelsInterface::class, new Channels());
    }
    
    public function testHasMethod()
    {
        $channels = new Channels(new NullChannel(name: 'foo'));

        $this->assertTrue($channels->has('foo'));
        $this->assertFalse($channels->has('baz'));
    }
    
    public function testGetMethod()
    {
        $foo = new NullChannel(name: 'foo');
        $channels = new Channels($foo);

        $this->assertSame($foo, $channels->get('foo'));
    }
    
    public function testGetMethodThrowsChannelNotFoundExceptionIfNotExists()
    {
        $this->expectException(ChannelNotFoundException::class);

        (new Channels())->get('foo');
    }
    
    public function testNamesMethod()
    {
        $channels = new Channels(new NullChannel(name: 'foo'), new NullChannel(name: 'bar'));

        $this->assertSame(['foo', 'bar'], $channels->names());
    }
    
    public function testAddMethod()
    {
        $channels = new Channels();
        $foo = new NullChannel(name: 'foo');
        $channels->add($foo);

        $this->assertSame($foo, $channels->get('foo'));
    }
}