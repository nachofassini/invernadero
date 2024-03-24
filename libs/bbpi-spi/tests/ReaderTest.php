<?php
namespace Volantus\MCP3008\Tests;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Volantus\BerrySpi\SpiInterface;
use Volantus\MCP3008\InvalidChannelException;
use Volantus\MCP3008\InvalidSpiDataException;
use Volantus\MCP3008\Reader;

/**
 * Class ReaderTest
 *
 * @package Volantus\MCP3008\Tests
 */
class ReaderTest extends TestCase
{
    /**
     * @var SpiInterface|MockObject
     */
    private MockObject|SpiInterface $spiInterface;

    /**
     * @var Reader
     */
    private Reader $reader;

    protected function setUp(): void
    {
        $this->spiInterface = $this->getMockBuilder(SpiInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['isOpen', 'open', 'close', 'transfer'])
            ->getMock();
        $this->spiInterface->method('isOpen')->willReturn(true);
        $this->reader = new Reader($this->spiInterface, 3.3);
    }

    public function test_construct_openedSpiDevice()
    {
        $this->spiInterface = $this->getMockBuilder(SpiInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['isOpen', 'open'])
            ->getMock();
        $this->spiInterface->method('isOpen')->willReturn(false);

        $this->spiInterface->expects(self::once())
            ->method('open');

        $this->reader = new Reader($this->spiInterface, 3.3);
    }

    public function test_read_badChannel()
    {
        $this->expectException(InvalidChannelException::class);
        $this->expectExceptionMessage("Invalid channel given => only channel between 0-7 supported");

        $this->reader->read(8);
    }

    public function test_read_invalidSpiData()
    {
        $this->expectException(InvalidSpiDataException::class);
        $this->expectExceptionMessage("Received bad binary data via SPI => [1,2,3,4], expected 3 words but received 4");

        $this->spiInterface->expects(self::once())
            ->method('transfer')
            ->with(self::equalTo([1, 192, 0]))
            ->willReturn([1, 2, 3, 4]);

        $this->reader->read(4);
    }

    public function test_read_correct()
    {
        $this->spiInterface->expects(self::once())
            ->method('transfer')
            ->with(self::equalTo([1, 208, 0]))
            ->willReturn([0, 3, 255]);

        $result =$this->reader->read(5);
        self::assertEquals(5, $result->getChannel());
        self::assertEquals(3.3, $result->getRefVoltage());
        self::assertEquals(1023, $result->getRawValue());
    }
}