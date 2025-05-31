<?php declare(strict_types=1);
namespace SepaQr;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Data::class)]
class DataTest extends TestCase
{
    public function testSetCharacterSet(): void
    {
        $sepaQrData = new Data();

        $sepaQrData->setCharacterSet(Data::ISO8859_1);

        $this->expectException(Exception::class);

        $sepaQrData->setCharacterSet(9);
    }

    public function testSetBic(): void
    {
        $sepaQrData = new Data();

        $sepaQrData->setBic('ABCDEFGH'); // 8 characters
        $sepaQrData->setBic('ABCDEFGHIJK'); // 11 characters

        $this->expectException(Exception::class);

        $sepaQrData->setBic('ABCDEFGHI'); // 9 characters
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

        $this->assertStringContainsString(
            'EUR1075.25',
            $message,
            'The amount should be formatted using only a dot (.) as the decimal separator'
        );

        $this->assertCount(
            11,
            explode("\n", $message),
            'The last populated element cannot be followed by any character or element separator'
        );

        $this->assertStringEndsWith(
            'DEF',
            $message,
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

    public function testSetVersionThrowsExceptionForInvalidValue(): void
    {
        $this->expectException(Exception::class);

        $sepaQrData = new Data();
        $sepaQrData->setVersion(3);
    }
}
