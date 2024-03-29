<?php
namespace Volantus\Pigpio\Tests\SPI;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Volantus\BerrySpi\SpiInterface;
use Volantus\Pigpio\Client;
use Volantus\Pigpio\Protocol\Commands;
use Volantus\Pigpio\Protocol\DefaultRequest;
use Volantus\Pigpio\Protocol\ExtensionRequest;
use Volantus\Pigpio\Protocol\ExtensionResponseStructure;
use Volantus\Pigpio\Protocol\Response;
use Volantus\Pigpio\SPI\BitBangingSpiDevice;
use Volantus\Pigpio\SPI\SpiFlags;

/**
 * Class BitBangingSpiDeviceTest
 *
 * @package Volantus\Pigpio\Tests\SPI
 */
class BitBangingSpiDeviceTest extends TestCase
{
    /**
     * @var Client|MockObject
     */
    private $client;

    /**
     * @var BitBangingSpiDevice
     */
    private $device;

    protected function setUp(): void
    {
        $this->client = $this->getMockBuilder(Client::class)->disableOriginalConstructor()->getMock();
        $this->device = new BitBangingSpiDevice($this->client, 6, 8, 21, 22, 32000, new SpiFlags(['notReserved' => [0]]));
    }

    public function test_constructor_flagsNull()
    {
        $this->device = new BitBangingSpiDevice($this->client, 6, 8, 21, 22, 32000, null);

        $this->client->expects(self::once())
            ->method('sendRaw')
            ->with(self::equalTo(new ExtensionRequest(Commands::BSPIO, 6, 0, 'LLLLL', [8, 21, 22, 32000, 0])))
            ->willReturn(new Response(0));

        $this->device->open();
    }

    public function test_open_correctRequest()
    {
        $this->client->expects(self::once())
            ->method('sendRaw')
            ->with(self::equalTo(new ExtensionRequest(Commands::BSPIO, 6, 0, 'LLLLL', [8, 21, 22, 32000, 32])))
            ->willReturn(new Response(0));

        $this->device->open();
        self::assertTrue($this->device->isOpen());
    }

    public function test_open_calledTwice_idempotent()
    {
        $this->client->expects(self::once())
            ->method('sendRaw')
            ->with(self::equalTo(new ExtensionRequest(Commands::BSPIO, 6, 0, 'LLLLL', [8, 21, 22, 32000, 32])))
            ->willReturn(new Response(0));

        $this->device->open();
        $this->device->open();
        self::assertTrue($this->device->isOpen());
    }

    public function test_open_badGpioPin()
    {
        $this->expectExceptionMessage("Opening device failed => bad GPIO pin given (PI_BAD_USER_GPIO)");
        $this->expectException(\Volantus\Pigpio\SPI\OpeningDeviceFailedException::class);
        $this->expectExceptionCode(BitBangingSpiDevice::PI_BAD_USER_GPIO);

        $this->client->expects(self::once())
            ->method('sendRaw')
            ->willReturn(new Response(BitBangingSpiDevice::PI_BAD_USER_GPIO));

        $this->device->open();
    }

    public function test_open_gpioAlreadyInUse()
    {
        $this->expectExceptionMessage("Opening device failed => GPIO pin is already in use (PI_GPIO_IN_USE)");
        $this->expectException(\Volantus\Pigpio\SPI\OpeningDeviceFailedException::class);
        $this->expectExceptionCode(BitBangingSpiDevice::PI_GPIO_IN_USE);

        $this->client->expects(self::once())
            ->method('sendRaw')
            ->willReturn(new Response(BitBangingSpiDevice::PI_GPIO_IN_USE));

        $this->device->open();
    }

    public function test_open_badBaudRate()
    {
        $this->expectExceptionMessage("Opening device failed => bad baud rate given (PI_BAD_SPI_BAUD)");
        $this->expectException(\Volantus\Pigpio\SPI\OpeningDeviceFailedException::class);
        $this->expectExceptionCode(BitBangingSpiDevice::PI_BAD_SPI_BAUD);

        $this->client->expects(self::once())
            ->method('sendRaw')
            ->willReturn(new Response(BitBangingSpiDevice::PI_BAD_SPI_BAUD));

        $this->device->open();
    }

    public function test_open_unknownError()
    {
        $this->expectExceptionMessage("Opening device failed => unknown error");
        $this->expectException(\Volantus\Pigpio\SPI\OpeningDeviceFailedException::class);
        $this->expectExceptionCode(-512);

        $this->client->expects(self::once())
            ->method('sendRaw')
            ->willReturn(new Response(-512));

        $this->device->open();
    }

    public function test_close_correctRequest()
    {
        $this->client->expects(self::at(0))
            ->method('sendRaw')
            ->willReturn(new Response(0));

        $this->client->expects(self::at(1))
            ->method('sendRaw')
            ->with(self::equalTo(new DefaultRequest(Commands::BSPIC, 6, 0)))
            ->willReturn(new Response(0));

        $this->device->open();
        $this->device->close();
        self::assertFalse($this->device->isOpen());
    }

    public function test_close_idempotent()
    {
        $this->client->expects(self::at(0))
            ->method('sendRaw')
            ->willReturn(new Response(0));

        $this->client->expects(self::at(1))
            ->method('sendRaw')
            ->with(self::equalTo(new DefaultRequest(Commands::BSPIC, 6, 0)))
            ->willReturn(new Response(0));

        $this->device->open();
        $this->device->close();
        $this->device->close();
        self::assertFalse($this->device->isOpen());
    }

    public function test_close_badGpioPin()
    {
        $this->expectExceptionMessage("Closing device failed (internal library error) => bad GPIO pin given (PI_BAD_USER_GPIO)");
        $this->expectException(\Volantus\Pigpio\SPI\ClosingDeviceFailedException::class);
        $this->expectExceptionCode(BitBangingSpiDevice::PI_BAD_USER_GPIO);

        $this->client->expects(self::at(0))
            ->method('sendRaw')
            ->willReturn(new Response(0));

        $this->client->expects(self::at(1))
            ->method('sendRaw')
            ->willReturn(new Response(BitBangingSpiDevice::PI_BAD_USER_GPIO));

        $this->device->open();
        $this->device->close();
    }

    public function test_close_noSpiInProgress()
    {
        $this->expectExceptionMessage("Closing device failed (internal library error) => no SPI action in progress on this pin (PI_NOT_SPI_GPIO)");
        $this->expectException(\Volantus\Pigpio\SPI\ClosingDeviceFailedException::class);
        $this->expectExceptionCode(BitBangingSpiDevice::PI_NOT_SPI_GPIO);

        $this->client->expects(self::at(0))
            ->method('sendRaw')
            ->willReturn(new Response(0));

        $this->client->expects(self::at(1))
            ->method('sendRaw')
            ->willReturn(new Response(BitBangingSpiDevice::PI_NOT_SPI_GPIO));

        $this->device->open();
        $this->device->close();
    }

    public function test_close_unknownError()
    {
        $this->expectExceptionMessage("Closing device failed => unknown error");
        $this->expectException(\Volantus\Pigpio\SPI\ClosingDeviceFailedException::class);
        $this->expectExceptionCode(-512);

        $this->client->expects(self::at(0))
            ->method('sendRaw')
            ->willReturn(new Response(0));

        $this->client->expects(self::at(1))
            ->method('sendRaw')
            ->willReturn(new Response(-512));

        $this->device->open();
        $this->device->close();
    }

    public function test_crossTransfer_correctRequest()
    {
        $this->client->expects(self::at(0))
            ->method('sendRaw')
            ->willReturn(new Response(0));

        $this->client->expects(self::at(1))
            ->method('sendRaw')
            ->with(self::equalTo(new ExtensionRequest(Commands::BSPIX, 6, 0, 'C*', [32, 64], new ExtensionResponseStructure('C*'))))
            ->willReturn(new Response(0, [16, 18, 19]));

        $this->device->open();
        $result = $this->device->crossTransfer([32, 64]);

        self::assertEquals([16, 18, 19], $result);
    }

    public function test_crossTransfer_notOpen()
    {
        $this->expectExceptionMessage("Device needs to be opened first for cross transfer");
        $this->expectException(\Volantus\Pigpio\SPI\DeviceNotOpenException::class);
        $this->device->crossTransfer([32]);
    }

    public function test_crossTransfer_badGpioPin()
    {
        $this->expectExceptionMessage("Cross transfer failed (internal library error) => bad GPIO pin given (PI_BAD_USER_GPIO)");
        $this->expectException(\Volantus\Pigpio\SPI\TransferFailedException::class);
        $this->expectExceptionCode(BitBangingSpiDevice::PI_BAD_USER_GPIO);

        $this->client->expects(self::at(0))
            ->method('sendRaw')
            ->willReturn(new Response(0));

        $this->client->expects(self::at(1))
            ->method('sendRaw')
            ->willReturn(new Response(BitBangingSpiDevice::PI_BAD_USER_GPIO));

        $this->device->open();
        $this->device->crossTransfer([32]);
    }

    public function test_crossTransfer_noSpiInProgress()
    {
        $this->expectExceptionMessage("Cross transfer failed (internal library error) => no SPI action in progress on this pin (PI_NOT_SPI_GPIO)");
        $this->expectException(\Volantus\Pigpio\SPI\TransferFailedException::class);
        $this->expectExceptionCode(BitBangingSpiDevice::PI_NOT_SPI_GPIO);

        $this->client->expects(self::at(0))
            ->method('sendRaw')
            ->willReturn(new Response(0));

        $this->client->expects(self::at(1))
            ->method('sendRaw')
            ->willReturn(new Response(BitBangingSpiDevice::PI_NOT_SPI_GPIO));

        $this->device->open();
        $this->device->crossTransfer([32]);
    }

    public function test_crossTransfer_unknownError()
    {
        $this->expectExceptionMessage("Cross transfer failed => unknown error");
        $this->expectException(\Volantus\Pigpio\SPI\TransferFailedException::class);
        $this->expectExceptionCode(-512);

        $this->client->expects(self::at(0))
            ->method('sendRaw')
            ->willReturn(new Response(0));

        $this->client->expects(self::at(1))
            ->method('sendRaw')
            ->willReturn(new Response(-512));

        $this->device->open();
        $this->device->crossTransfer([32]);
    }

    public function test_implementsInterface()
    {
        self::assertInstanceOf(SpiInterface::class, $this->device);
    }
}