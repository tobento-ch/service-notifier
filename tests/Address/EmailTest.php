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
use Tobento\Service\Notifier\Address\Email;
use Tobento\Service\Notifier\Address\EmailInterface;

class EmailTest extends TestCase
{
    public function testThatImplementsEmailInterface()
    {
        $this->assertInstanceof(EmailInterface::class, new Email(email: 'from@example.com'));
    }
    
    public function testInterfaceMethods()
    {
        $email = new Email(email: 'from@example.com');
        
        $this->assertSame('from@example.com', $email->email());
        $this->assertSame(null, $email->name());
        
        $email = new Email(email: 'from@example.com', name: 'From');
        
        $this->assertSame('from@example.com', $email->email());
        $this->assertSame('From', $email->name());
    }
}