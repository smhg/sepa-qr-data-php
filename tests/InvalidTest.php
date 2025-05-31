<?php declare(strict_types=1);

namespace SepaQr;

use InvalidArgumentException;
use LogicException;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(SepaQrData::class)]
final class InvalidTest extends TestCase
{
    public function testFailServiceTag(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid service tag: ABC. Should be BCD.');

        $this->data->setServiceTag('ABC');
    }

    public function testFailSetVersion(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid version: 3. Should be either 1 or 2.');

        $this->data->setVersion(3);
    }

    public function testFailSetCharacterSet(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid character set: 9. Should be between 1 and 8.');

        $this->data->setCharacterSet(9);
    }

    public function testFailSetIdentification(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid identification code: ABC. Should be SCT.');

        $this->data->setIdentification('ABC');
    }

    public function testFailSetBic(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid BIC of the beneficiary: ABCDEFGHI. Should be either 8 or 11 characters.');

        $this->data->setBic('ABCDEFGHI'); // 9 characters
    }

    public function testFailSetName(): void
    {
        $name = str_repeat('X', 75);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid name of the beneficiary: $name. Should be maximum 70 characters.");

        $this->data->setName($name);
    }

    public function testFailSetIban(): void
    {
        $iban = str_repeat('X', 35);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid account number of the beneficiary: $iban. Should be maximum 34 characters.");

        $this->data->setIban($iban);
    }

    public function testFailSetCurrency(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid currency: ABCD. Should be a valid 3 character ISO 4217 code.');

        $this->data->setCurrency('ABCD');
    }

    public function testFailSetAmountLow(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid amount: 0. Should be minumum 0.01 Euro.');

        $this->data->setAmount(0);
    }

    public function testFailSetAmountHigh(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid amount: 1000000000. Should be maximum 999999999.99 Euro.');

        $this->data->setAmount(1000000000);
    }

    public function testFailSetPurpose(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid purpose code: INVALID. Should be 4 characters.');

        $this->data->setPurpose('INVALID');
    }

    public function testFailSetRemittanceReference(): void
    {
        $reference = str_repeat('X', 40);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid structured remittance information: $reference. Should be maximum 35 characters.");

        $this->data->setRemittanceReference($reference);
    }

    public function testFailSetRemittanceTextAndRemittanceReference(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid remittance information. Use either structured or unstructured remittance information, not both.');

        $this->data->setRemittanceText('DEF')
            ->setRemittanceReference('ABC');
    }

    public function testFailSetRemittanceText(): void
    {
        $text = str_repeat('X', 150);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid unstructured remittance information: $text. Should be maximum 140 characters.");

        $this->data->setRemittanceText($text);
    }

    public function testFailSetRemittanceReferenceAndRemittanceText(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid remittance information. Use either structured or unstructured remittance information, not both.');

        $this->data->setRemittanceReference('ABC')
            ->setRemittanceText('DEF');
    }

    public function testFailSetInformation(): void
    {
        $information = str_repeat('X', 75);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid beneficiary to originator information: $information. Should be maximum 70 characters.");

        $this->data->setInformation($information);
    }

    public function testFailEncodeMessageMissingName(): void
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Name of the beneficiary is required.');

        (string)$this->data;
    }

    public function testFailEncodeMessageMissingIban(): void
    {
        $this->data->setName('ABC');

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('Account number of the beneficiary is required.');

        (string)$this->data;
    }

    public function testFailEncodeMessageVersion1MissingBic(): void
    {
        $this->data->setVersion(1)
            ->setName('ABC')
            ->setIban('DEF');

        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('BIC of the beneficiary is required.');

        (string)$this->data;
    }
}
