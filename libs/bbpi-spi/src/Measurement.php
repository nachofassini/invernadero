<?php
namespace Volantus\MCP3008;

/**
 * Class Measurement
 *
 * @package Volantus\MCP3008
 */
class Measurement
{
    /**
     * Resolution of the ADC MCP3008
     * 10 Bits => 2^10 => 1023
     */
    const RESOLUTION = 1023;

    /**
     * @var int ADC channel
     */
    private $channel;

    /**
     * @var float UNIX micro time
     */
    private $timestamp;

    /**
     * @var float
     */
    private $refVoltage;

    /**
     * @var int
     */
    private $rawValue;

    /**
     * @param int   $channel
     * @param int   $rawValue   raw value measured by the ADC
     * @param float $timestamp  UNIX micro timestamp of the measurement, if null current timestamp will be used
     * @param float $refVoltage reference voltage used by the ADC
     */
    public function __construct(int $channel, int $rawValue, float $timestamp = null, float $refVoltage = 3.3)
    {
        $this->channel = $channel;
        $this->timestamp = $timestamp ?: microtime(true);
        $this->refVoltage = $refVoltage;
        $this->rawValue = $rawValue;
    }

    /**
     * @return int
     */
    public function getChannel(): int
    {
        return $this->channel;
    }


    /**
     * @return float
     */
    public function getTimestamp(): float
    {
        return $this->timestamp;
    }

    /**
     * @return float
     */
    public function getRefVoltage(): float
    {
        return $this->refVoltage;
    }

    /**
     * @return int
     */
    public function getRawValue(): int
    {
        return $this->rawValue;
    }

    /**
     * @return float
     */
    public function calculateVoltage(): float
    {
        return ($this->rawValue / self::RESOLUTION) * $this->refVoltage;
    }
}