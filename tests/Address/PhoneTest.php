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

namespace Tobento\Service\Notifier\Test\Address;

use PHPUnit\Framework\TestCase;
use Tobento\Service\Notifier\Address\Phone;
use Tobento\Service\Notifier\Address\PhoneInterface;

class PhoneTest extends TestCase
{
    public function testThatImplementsPhoneInterface()
    {
        $this->assertInstanceof(PhoneInterface::class, new Phone(phone: '123456'));
    }
    
    public function testInterfaceMethods()
    {
        $phone = new Phone(phone: '123456');
        
        $this->assertSame('123456', $phone->phone());
        $this->assertSame(null, $phone->name());
        
        $phone = new Phone(phone: '123456', name: 'Name');
        
        $this->assertSame('123456', $phone->phone());
        $this->assertSame('Name', $phone->name());
    }
}