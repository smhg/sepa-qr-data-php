<?php
namespace SepaQr\Test;

use PHPUnit\Framework\TestCase;
use SepaQr\Data;
use SepaQr\Exception;

class DataTest extends TestCase
{
    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function testFormatMoney(): void
    {
        $this->assertEquals(
            'EUR100.00',
            Data::formatMoney('EUR', 100),
            'An amount should be formatted'
        );

        $this->assertEquals(
            'EUR',
            Data::formatMoney('EUR'),
            'A missing amount should only return the currency'
        );
    }

    /**
     * @SuppressWarnings(PHPMD.StaticAccess)
     */
    public function testCreate(): void
    {
        $this->assertInstanceOf(
            Data::class,
            Data::create()
        );
    }

    public function testSetCharacterSet(): void
    {
        $sepaQrData = new Data();

        $sepaQrData->setCharacterSet(Data::ISO8859_1);

        $this->expectException(\TypeError::class);

        $sepaQrData->setCharacterSet('UTF8');
    }

    public function testSetCurrency(): void
    {
        $sepaQrData = new Data();

        $sepaQrData->setCurrency('USD');

        $this->expectException(Exception::class);

        $sepaQrData->setCurrency('ABCDEF');
    }

    public function testSetRemittance(): void
    {
        $sepaQrData = new Data();

        $this->expectException(Exception::class);

        $sepaQrData->setRemittanceReference('ABC')
            ->setRemittanceText('DEF');
    }

    public function testSetPurpose(): void
    {
        $sepaQrData = new Data();

        $sepaQrData->setPurpose('ACMT');

        $this->expectException(Exception::class);

        $sepaQrData->setPurpose('custom');
    }

    public function testEncodeMessage(): void
    {
        $sepaQrData = new Data();

        $sepaQrData->setName('Test')
            ->setIban('ABC')
            ->setAmount(1075.25)
            ->setRemittanceText('DEF');

        $message = (string)$sepaQrData;

        $this->assertTrue(
            stristr($message, '1075.25') !== false,
            'The amount should be formatted using only a dot (.) as the decimal separator'
        );

        $this->assertEquals(
            11,
            count(explode("\n", $message)),
            'The last populated element cannot be followed by any character or element separator'
        );

        $this->assertTrue(
            substr($message, strlen($message) - 3) === 'DEF',
            'The last populated element cannot be followed by any character or element separator'
        );

        $expectedString = <<<EOF
BCD
002
1
SCT

Test
ABC
EUR1075.25


DEF
EOF;

        $this->assertSame($expectedString, $message);
    }

    public function testToString(): void
    {
        $sepaQrData = new Data();

        $this->assertIsString(
            (string)$sepaQrData->setName('Test')
                ->setIban('ABC')
        );
    }

    public function testSetVersionExceptionCase1(): void
    {
        $this->expectException(Exception::class);

        $sepaQrData = new Data();
        $sepaQrData->setVersion(3);
    }

    public function testSetVersionExceptionCase2(): void
    {
        $this->expectException(\TypeError::class);

        $sepaQrData = new Data();
        $sepaQrData->setVersion('v1');
    }
}
