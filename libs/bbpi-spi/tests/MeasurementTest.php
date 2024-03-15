<?php
namespace Volantus\MCP3008\Tests;

use PHPUnit\Framework\TestCase;
use Volantus\MCP3008\Measurement;

/**
 * Class MeasurementTest
 *
 * @package Volantus\MCP3008\Tests
 */
class MeasurementTest extends TestCase
{
    public function test_calculateVoltage_belowRef()
    {
        $measurement = new Measurement(1, 789, null, 3.3);
        $result = round($measurement->calculateVoltage(), 3);
        self::assertEquals(2.545, $result);
    }

    public function test_calculateVoltage_aboveRef()
    {
        $measurement = new Measurement(1, 1597, null, 3.3);
        $result = round($measurement->calculateVoltage(), 3);
        self::assertEquals(5.152, $result);
    }

    public function test_calculateVoltage_zero()
    {
        $measurement = new Measurement(1, 0, null, 3.3);
        self::assertEquals(0, $measurement->calculateVoltage());
    }
}