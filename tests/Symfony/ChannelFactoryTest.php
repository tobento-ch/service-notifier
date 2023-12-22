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
use Tobento\Service\Notifier\Symfony\ChannelFactory;
use Tobento\Service\Notifier\ChannelFactoryInterface;
use Tobento\Service\Notifier\ChannelInterface;
use Tobento\Service\Notifier\Exception\ChannelCreateException;
use Tobento\Service\Container\Container;
use Symfony\Component\Notifier\Channel\SmsChannel;

class ChannelFactoryTest extends TestCase
{
    public function testThatImplementsChannelFactoryInterface()
    {
        $factory = new ChannelFactory(container: new Container());
        
        $this->assertInstanceof(ChannelFactoryInterface::class, $factory);
    }
    
    public function testCreateChannel()
    {
        $factory = new ChannelFactory(container: new Container());
        
        $channel = $factory->createChannel(
            name: 'sms',
            config: [
                'dsn' => 'vonage://KEY:SECRET@default?from=FROM',
                'channel' => SmsChannel::class,
            ],
        );
        
        $this->assertInstanceof(ChannelInterface::class, $channel);
        $this->assertSame('sms', $channel->name());
    }

    public function testCreateChannelThrowsChannelCreateExceptionIfConfigDsnMissing()
    {
        $this->expectException(ChannelCreateException::class);
        $this->expectExceptionMessage('Channel config "dsn" for name sms does not exist');
        
        $factory = new ChannelFactory(container: new Container());
        
        $factory->createChannel(
            name: 'sms',
            config: [
                'channel' => SmsChannel::class,
            ],
        );
    }
    
    public function testCreateChannelThrowsChannelCreateExceptionIfConfigChannelMissing()
    {
        $this->expectException(ChannelCreateException::class);
        $this->expectExceptionMessage('Channel config "channel" for name sms does not exist');
        
        $factory = new ChannelFactory(container: new Container());
        
        $factory->createChannel(
            name: 'sms',
            config: [
                'dsn' => 'vonage://KEY:SECRET@default?from=FROM',
            ],
        );
    }
    
    public function testCreateChannelThrowsChannelCreateExceptionIfInvalidChannel()
    {
        $this->expectException(ChannelCreateException::class);
        
        $factory = new ChannelFactory(container: new Container());
        
        $factory->createChannel(
            name: 'sms',
            config: [
                'dsn' => 'vonage://KEY:SECRET@default?from=FROM',
                'channel' => InvalidChannel::class,
            ],
        );
    }
    
    public function testCreateChannelThrowsChannelCreateExceptionIfSchemeIsUnsupported()
    {
        $this->expectException(ChannelCreateException::class);
        
        $factory = new ChannelFactory(container: new Container());
        
        $factory->createChannel(
            name: 'sms',
            config: [
                'dsn' => 'invalid://KEY:SECRET@default?from=FROM',
                'channel' => SmsChannel::class,
            ],
        );
    }
}