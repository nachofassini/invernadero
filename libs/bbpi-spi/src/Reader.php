<?php
namespace Volantus\MCP3008;

use Volantus\BerrySpi\SpiInterface;

/**
 * Class Reader
 *
 * @package Volantus\MCP3008
 */
class Reader
{
    /**
     * @var SpiInterface
     */
    private $spiInterface;

    /**
     * @var float
     */
    private $refVoltage;

    /**
     * Reader constructor.
     *
     * @param SpiInterface $spiInterface
     * @param float        $refVoltage
     */
    public function __construct(SpiInterface $spiInterface, float $refVoltage = 3.3)
    {
        $this->spiInterface = $spiInterface;
        $this->refVoltage = $refVoltage;

        if (!$this->spiInterface->isOpen()) {
            $this->spiInterface->open();
        }
    }

    public function __destruct()
    {
        try {
            if ($this->spiInterface->isOpen()) {
                $this->spiInterface->close();
            }
        } catch (\Throwable $e) {}
     }

    /**
     * @param int $channel
     *
     * @return Measurement
     */
    public function read(int $channel): Measurement
    {
        if ($channel < 0 || $channel > 7) {
            throw new InvalidChannelException('Invalid channel given => only channel between 0-7 supported');
        }

        $data = $this->spiInterface->transfer([1, (8 + $channel) << 4, 0]);

        if (count($data) !== 3) {
            throw new InvalidSpiDataException('Received bad binary data via SPI => ' . json_encode($data, true). ', expected 3 words but received ' . count($data));
        }

        $adcValue = (($data[1] & 3) << 8) + $data[2];

        return new Measurement($channel, $adcValue, null, $this->refVoltage);
    }
}