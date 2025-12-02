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
use Tobento\Service\Notifier\Address\Dsn;
use Tobento\Service\Notifier\Address\DsnInterface;

class DsnTest extends TestCase
{
    public function testThatImplementsDsnInterface()
    {
        $this->assertInstanceof(DsnInterface::class, new Dsn(dsn: 'slack://TOKEN@default?channel=CHANNEL'));
    }
    
    public function testInterfaceMethods()
    {
        $dsn = new Dsn(dsn: 'slack://TOKEN@default?channel=CHANNEL');
        
        $this->assertSame('slack://TOKEN@default?channel=CHANNEL', $dsn->dsn());
        $this->assertSame(null, $dsn->name());
        
        $dsn = new Dsn(dsn: 'slack://TOKEN@default?channel=CHANNEL', name: 'Name');
        
        $this->assertSame('slack://TOKEN@default?channel=CHANNEL', $dsn->dsn());
        $this->assertSame('Name', $dsn->name());
    }
}