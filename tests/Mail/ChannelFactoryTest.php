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

namespace Tobento\Service\Notifier\Test\Mail;

use PHPUnit\Framework\TestCase;
use Tobento\Service\Notifier\Mail\ChannelFactory;
use Tobento\Service\Notifier\ChannelFactoryInterface;
use Tobento\Service\Notifier\ChannelInterface;
use Tobento\Service\Notifier\Exception\ChannelCreateException;
use Tobento\Service\Mail\Mailers;
use Tobento\Service\Mail\NullMailer;
use Tobento\Service\Container\Container;

class ChannelFactoryTest extends TestCase
{
    public function testThatImplementsChannelFactoryInterface()
    {
        $factory = new ChannelFactory(
            mailer: new NullMailer('null'),
            container: new Container(),
        );
        
        $this->assertInstanceof(ChannelFactoryInterface::class, $factory);
    }
    
    public function testCreateChannelWithMailer()
    {
        $mailer = new NullMailer('null');
        $factory = new ChannelFactory(
            mailer: $mailer,
            container: new Container(),
        );
        
        $channel = $factory->createChannel(name: 'mail');
        
        $this->assertInstanceof(ChannelInterface::class, $channel);
        $this->assertSame('mail', $channel->name());
        $this->assertSame($mailer, $channel->mailer());
    }
    
    public function testCreateChannelWithMailerAndMailerConfigUsesMailerIfNotExist()
    {
        $mailer = new NullMailer('null');
        $factory = new ChannelFactory(
            mailer: $mailer,
            container: new Container(),
        );
        
        $channel = $factory->createChannel(name: 'mail', config: ['mailer' => 'name']);
        
        $this->assertSame('mail', $channel->name());
        $this->assertSame($mailer, $channel->mailer());
    }
    
    public function testCreateChannelWithMailers()
    {
        $mailers = new Mailers(
            new NullMailer('foo'),
            new NullMailer('bar'),
        );
        
        $factory = new ChannelFactory(
            mailer: $mailers,
            container: new Container(),
        );
        
        $channel = $factory->createChannel(name: 'mail');
        
        $this->assertSame('mail', $channel->name());
        $this->assertSame($mailers, $channel->mailer());
    }
    
    public function testCreateChannelWithMailersUsingSpecificMailer()
    {
        $mailers = new Mailers(
            new NullMailer('foo'),
            new NullMailer('bar'),
        );
        
        $factory = new ChannelFactory(
            mailer: $mailers,
            container: new Container(),
        );
        
        $channel = $factory->createChannel(name: 'mail', config: ['mailer' => 'bar']);
        
        $this->assertSame('mail', $channel->name());
        $this->assertSame($mailers->mailer('bar'), $channel->mailer());
    }
    
    public function testCreateChannelWithMailersUsingSpecificMailerThrowsExceptionIfNotExist()
    {
        $this->expectException(ChannelCreateException::class);
        
        $mailers = new Mailers(
            new NullMailer('foo'),
        );
        
        $factory = new ChannelFactory(
            mailer: $mailers,
            container: new Container(),
        );
        
        $channel = $factory->createChannel(name: 'mail', config: ['mailer' => 'bar']);
    }
}