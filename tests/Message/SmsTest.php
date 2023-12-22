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
use Tobento\Service\Notifier\Message\Sms;
use Tobento\Service\Notifier\Message\SmsInterface;
use Tobento\Service\Notifier\ParametersInterface;
use Tobento\Service\Notifier\Parameter\Queue;
use Tobento\Service\Notifier\Address\PhoneInterface;
use Tobento\Service\Notifier\Address\Phone;

class SmsTest extends TestCase
{
    public function testThatImplementsSmsInterface()
    {
        $this->assertInstanceof(SmsInterface::class, new Sms());
    }
    
    public function testInterfaceMethods()
    {
        $msg = new Sms(subject: 'Subject');
        
        $this->assertSame('Subject', $msg->getSubject());
        $this->assertSame(null, $msg->getFrom());
        $this->assertSame(null, $msg->getTo());
        $this->assertInstanceof(ParametersInterface::class, $msg->parameters());
        $msg->parameter(new Queue());
    }
    
    public function testGetFromMethod()
    {
        $msg = new Sms(from: '556677');
        $this->assertSame('556677', $msg->getFrom()?->phone());
        
        $phone = new Phone('556677');
        $msg = new Sms(from: $phone);
        $this->assertSame($phone, $msg->getFrom());
    }
    
    public function testFromMethod()
    {
        $msg = new Sms(from: '556677');
        $msg->from('889911');
        $this->assertSame('889911', $msg->getFrom()?->phone());
        
        $phone = new Phone('556677');
        $msg = new Sms(from: '556677');
        $msg->from($phone);
        $this->assertSame($phone, $msg->getFrom());
    }
    
    public function testGetToMethod()
    {
        $msg = new Sms(to: '556677');
        $this->assertSame('556677', $msg->getTo()?->phone());
        
        $phone = new Phone('556677');
        $msg = new Sms(to: $phone);
        $this->assertSame($phone, $msg->getTo());
    }
    
    public function testToMethod()
    {
        $msg = new Sms(to: '556677');
        $msg->to('889911');
        $this->assertSame('889911', $msg->getTo()?->phone());
        
        $phone = new Phone('556677');
        $msg = new Sms(to: '556677');
        $msg->to($phone);
        $this->assertSame($phone, $msg->getTo());
    }
}