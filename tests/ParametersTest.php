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
use Tobento\Service\Notifier\Parameters;
use Tobento\Service\Notifier\ParametersInterface;
use Tobento\Service\Notifier\ParameterInterface;
use Tobento\Service\Notifier\Parameter\Queue;

class ParametersTest extends TestCase
{
    public function testThatImplementsParametersInterface()
    {
        $this->assertInstanceof(ParametersInterface::class, new Parameters());
    }
    
    public function testAddMethod()
    {
        $foo = new Queue(name: 'foo');
        $bar = new Queue(name: 'bar');
        $parameters = (new Parameters())->add($foo)->add($bar);
        
        $this->assertTrue($foo === ($parameters->all()[0] ?? null));
        $this->assertTrue($bar === ($parameters->all()[1] ?? null));
        $this->assertSame(2, count($parameters->all()));
    }
    
    public function testRemoveMethod()
    {
        $foo = new Queue(name: 'foo');
        $bar = new Queue(name: 'bar');
        $parameters = new Parameters($foo, $bar);
        
        $this->assertSame(2, count($parameters->all()));
        
        $parameters->remove(Queue::class);
        
        $this->assertSame(0, count($parameters->all()));
    }
    
    public function testFilterMethod()
    {
        $parameters = new Parameters(
            new Queue(name: 'foo'),
            new Queue(name: 'bar'),
        );
        
        $parametersNew = $parameters->filter(
            fn(ParameterInterface $p): bool => $p instanceof Queue && $p->name() === 'bar'
        );
        
        $this->assertFalse($parameters === $parametersNew);
        $this->assertSame(1, count($parametersNew->all()));
        $this->assertSame(2, count($parameters->all()));
    }
    
    public function testNameMethod()
    {
        $parameters = new Parameters(
            new Queue(name: 'foo'),
        );
        
        $parametersNew = $parameters->name(Queue::class);
        
        $this->assertFalse($parameters === $parametersNew);
        $this->assertSame(1, count($parametersNew->all()));
    }
    
    public function testFirstMethod()
    {
        $parameters = new Parameters(
            new Queue(name: 'foo'),
            new Queue(name: 'bar'),
        );
        
        $this->assertInstanceof(ParameterInterface::class, $parameters->first());
        
        $parameters = new Parameters();
        
        $this->assertSame(null, $parameters->first());
    }
    
    public function testAllMethod()
    {
        $parameters = new Parameters();
        
        $this->assertSame(0, count($parameters->all()));
        
        $parameters = new Parameters(
            new Queue(name: 'foo'),
        );
        
        $this->assertSame(1, count($parameters->all()));
    }
    
    public function testIteration()
    {
        $parameters = new Parameters(
            new Queue(name: 'foo'),
            new Queue(name: 'bar'),
        );
        
        foreach($parameters as $parameter) {
            $this->assertInstanceof(ParameterInterface::class, $parameter);
        }
    }
}