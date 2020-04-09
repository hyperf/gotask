<?php

declare(strict_types=1);
/**
 * This file is part of Reasno/GoTask.
 *
 * @link     https://www.github.com/reasno/gotask
 * @document  https://www.github.com/reasno/gotask
 * @contact  guxi99@gmail.com
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace HyperfTest\Cases;

use Mockery;
use PHPUnit\Framework\TestCase;
use Reasno\GoTask\GoTask;
use Reasno\GoTask\GoTaskProxy;

/**
 * @internal
 * @coversNothing
 */
class GoTaskProxyTest extends TestCase
{
    public function testCall()
    {
        $goTask = Mockery::namedMock('MyClassName', GoTask::class);
        $goTask->shouldReceive('call')->once()->withArgs(['a', 'b', 0]);
        $goTask->shouldReceive('call')->once()->withArgs(['GoTaskProxy.Test', 'mmm', 1]);
        $proxy = new GoTaskProxy($goTask);
        $proxy->call('a', 'b', 0);
        $proxy->test('mmm', 1);
        $this->assertFalse(false);
    }
}
