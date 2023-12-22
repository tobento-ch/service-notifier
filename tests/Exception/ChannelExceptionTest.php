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
use Tobento\Service\Notifier\Exception\ChannelException;
use RuntimeException;

class ChannelExceptionTest extends TestCase
{
    public function testException()
    {
        $this->assertInstanceof(RuntimeException::class, new ChannelException());
    }
}