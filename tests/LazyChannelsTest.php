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
use Tobento\Service\Notifier\LazyChannels;
use Tobento\Service\Notifier\ChannelsInterface;
use Tobento\Service\Notifier\ChannelInterface;
use Tobento\Service\Notifier\NullChannel;
use Tobento\Service\Notifier\Symfony;
use Tobento\Service\Notifier\Exception\ChannelNotFoundException;
use Tobento\Service\Notifier\Exception\ChannelCreateException;
use Tobento\Service\Container\Container;
use Psr\Container\ContainerInterface;

class LazyChannelsTest extends TestCase
{
    public function testThatImplementsChannelsInterface()
    {
        $channels = new LazyChannels(
            container: new Container(),
            channels: [],
        );
        
        $this->assertInstanceof(ChannelsInterface::class, $channels);
    }
    
    public function testHasMethod()
    {
        $foo = new NullChannel(name: 'foo');
        $channels = new LazyChannels(
            container: new Container(),
            channels: [
                'foo' => $foo,
            ],
        );

        $this->assertTrue($channels->has('foo'));
        $this->assertFalse($channels->has('baz'));
    }
    
    public function testGetMethod()
    {
        $foo = new NullChannel(name: 'foo');
        $channels = new LazyChannels(
            container: new Container(),
            channels: [
                'foo' => $foo,
            ],
        );

        $this->assertSame($foo, $channels->get('foo'));
    }
    
    public function testGetMethodThrowsChannelNotFoundExceptionIfNotExists()
    {
        $this->expectException(ChannelNotFoundException::class);

        (new LazyChannels(
            container: new Container(),
            channels: [],
        ))->get('foo');
    }
    
    public function testNamesMethod()
    {
        $channels = new LazyChannels(
            container: new Container(),
            channels: [
                'foo' => new NullChannel(name: 'foo'),
                'bar' => fn () => new NullChannel(name: 'bar'),
            ],
        );

        $this->assertSame(['foo', 'bar'], $channels->names());
    }
    
    public function testUsingObject()
    {
        $channels = new LazyChannels(
            container: new Container(),
            channels: [
                'foo' => new NullChannel(name: 'foo'),
            ],
        );

        $this->assertInstanceof(ChannelInterface::class, $channels->get('foo'));
        $this->assertSame($channels->get('foo'), $channels->get('foo'));
    }
    
    public function testUsingClosure()
    {
        $channels = new LazyChannels(
            container: new Container(),
            channels: [
                'foo' => static function (string $name, ContainerInterface $c): ChannelInterface {
                    return new NullChannel(name: $name);
                },
            ],
        );

        $this->assertInstanceof(ChannelInterface::class, $channels->get('foo'));
        $this->assertSame($channels->get('foo'), $channels->get('foo'));
    }
    
    public function testUsingClosureThrowsChannelCreateExceptionOnFailure()
    {
        $this->expectException(ChannelCreateException::class);
        
        (new LazyChannels(
            container: new Container(),
            channels: [
                'foo' => static function (string $name, ContainerInterface $c): ChannelInterface {
                    throw new \Exception();
                },
            ],
        ))->get('foo');
    }    
    
    public function testUsingFactory()
    {
        $channels = new LazyChannels(
            container: new Container(),
            channels: [
                'sms' => [
                    'factory' => Symfony\ChannelFactory::class,
                    'config' => [
                        'dsn' => 'vonage://KEY:SECRET@default?from=FROM',
                        'channel' => \Symfony\Component\Notifier\Channel\SmsChannel::class,
                    ],
                ],
            ],
        );

        $this->assertInstanceof(ChannelInterface::class, $channels->get('sms'));
        $this->assertSame($channels->get('sms'), $channels->get('sms'));
    }
    
    public function testUsingFactoryThrowsChannelCreateExceptionIfConfigFactoryMissing()
    {
        $this->expectException(ChannelCreateException::class);
        $this->expectExceptionMessage('Missing channel factory on "sms" channel');
        
        (new LazyChannels(
            container: new Container(),
            channels: [
                'sms' => [
                    'config' => [
                        'dsn' => 'vonage://KEY:SECRET@default?from=FROM',
                        'channel' => \Symfony\Component\Notifier\Channel\SmsChannel::class,
                    ],
                ],
            ],
        ))->get('sms');
    }
    
    public function testUsingFactoryThrowsChannelCreateExceptionIfConfigMissing()
    {
        $this->expectException(ChannelCreateException::class);
        
        (new LazyChannels(
            container: new Container(),
            channels: [
                'sms' => [
                    'factory' => Symfony\ChannelFactory::class,
                    'config' => [],
                ],
            ],
        ))->get('sms');
    }
}