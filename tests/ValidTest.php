<?php declare(strict_types=1);

namespace SepaQr;

use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(SepaQrData::class)]
final class ValidTest extends TestCase
{
    public function testServiceTag(): void
    {
        $result = $this->data->setServiceTag('BCD');

        $this->assertSame($this->data, $result, 'setServiceTag should return self for chaining');
    }

    public function testSetVersion(): void
    {
        $result = $this->data->setVersion(1);

        $this->assertSame($this->data, $result, 'setVersion should return self for chaining');
    }

    public function testSetCharacterSet(): void
    {
        $result = $this->data->setCharacterSet(SepaQrData::ISO8859_1);

        $this->assertSame($this->data, $result, 'setCharacterSet should return self for chaining');
    }

    public function testSetIdentification(): void
    {
        $result = $this->data->setIdentification('SCT');

        $this->assertSame($this->data, $result, 'setIdentification should return self for chaining');
    }

    public function testSetBic(): void
    {
        $this->data->setBic('ABCDEFGH'); // 8 characters
        $result = $this->data->setBic('ABCDEFGHIJK'); // 11 characters

        $this->assertSame($this->data, $result, 'setBic should return self for chaining');
    }

    public function testSetName(): void
    {
        $result = $this->data->setName('ABC');

        $this->assertSame($this->data, $result, 'setName should return self for chaining');
    }

    public function testSetIban(): void
    {
        $result = $this->data->setIban('ABC');

        $this->assertSame($this->data, $result, 'setIban should return self for chaining');
    }

    public function testSetCurrency(): void
    {
        $result = $this->data->setCurrency('USD');

        $this->assertSame($this->data, $result, 'setCurrency should return self for chaining');
    }

    public function testSetRemittanceReference(): void
    {
        $result = $this->data->setRemittanceReference('ABC');

        $this->assertSame($this->data, $result, 'setRemittanceReference should return self for chaining');
    }

    public function testSetRemittanceText(): void
    {
        $result = $this->data->setRemittanceText('ABC');

        $this->assertSame($this->data, $result, 'setRemittanceText should return self for chaining');
    }

    public function testSetAmount(): void
    {
        $result = $this->data->setAmount(100);

        $this->assertSame($this->data, $result, 'setAmount should return self for chaining');
    }

    public function testSetPurpose(): void
    {
        $result = $this->data->setPurpose('ACMT');

        $this->assertSame($this->data, $result, 'setPurpose should return self for chaining');
    }

    public function testSetInformation(): void
    {
        $result = $this->data->setInformation('ABC');

        $this->assertSame($this->data, $result, 'setInformation should return self for chaining');
    }

    public function testEncodeMessage(): void
    {
        $this->data->setName('Test')
            ->setIban('ABC')
            ->setAmount(1075.25)
            ->setRemittanceText('DEF');

        $message = (string)$this->data;

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
}
