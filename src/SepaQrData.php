<?php declare(strict_types=1);

namespace SepaQr;

use InvalidArgumentException;
use LogicException;

class SepaQrData
{
    const UTF_8 = 1;
    const ISO8859_1 = 2;
    const ISO8859_2 = 3;
    const ISO8859_4 = 4;
    const ISO8859_5 = 5;
    const ISO8859_7 = 6;
    const ISO8859_10 = 7;
    const ISO8859_15 = 8;

    /**
     * @var array<string, int|float|string>
     */
    private $sepaValues = array(
        'serviceTag' => 'BCD',
        'version' => 2,
        'characterSet' => 1,
        'identification' => 'SCT'
    );

    protected function formatMoney(float $value = 0): string
    {
        /** @var string */
        $currency = $this->sepaValues['currency'] ?? 'EUR';

        return sprintf(
            '%s%s',
            strtoupper($currency),
            $value > 0 ? number_format($value, 2, '.', '') : ''
        );
    }

    public function setServiceTag(string $serviceTag = 'BCD'): static
    {
        if ($serviceTag !== 'BCD') {
            throw new InvalidArgumentException("Invalid service tag: $serviceTag. Should be BCD.");
        }

        $this->sepaValues['serviceTag'] = $serviceTag;

        return $this;
    }

    public function setVersion(int $version = 2): static
    {
        if ($version < 1 || $version > 2) {
            throw new InvalidArgumentException("Invalid version: $version. Should be either 1 or 2.");
        }

        $this->sepaValues['version'] = $version;

        return $this;
    }

    public function setCharacterSet(int $characterSet = self::UTF_8): static
    {
        if ($characterSet < 1 || $characterSet > 8) {
            throw new InvalidArgumentException("Invalid character set: $characterSet. Should be between 1 and 8.");
        }

        $this->sepaValues['characterSet'] = $characterSet;

        return $this;
    }

    public function setIdentification(string $identification = 'SCT'): static
    {
        if ($identification !== 'SCT') {
            throw new InvalidArgumentException("Invalid identification code: $identification. Should be SCT.");
        }

        $this->sepaValues['identification'] = $identification;

        return $this;
    }

    public function setBic(string $bic): static
    {
        if (strlen($bic) !== 8 && strlen($bic) !== 11) {
            throw new InvalidArgumentException("Invalid BIC of the beneficiary: $bic. Should be either 8 or 11 characters.");
        }

        $this->sepaValues['bic'] = $bic;

        return $this;
    }

    public function setName(string $name): static
    {
        if (strlen($name) > 70) {
            throw new InvalidArgumentException("Invalid name of the beneficiary: $name. Should be maximum 70 characters.");
        }

        $this->sepaValues['name'] = $name;

        return $this;
    }

    public function setIban(string $iban): static
    {
        if (strlen($iban) > 34) {
            throw new InvalidArgumentException("Invalid account number of the beneficiary: $iban. Should be maximum 34 characters.");
        }

        $this->sepaValues['iban'] = $iban;

        return $this;
    }

    public function setCurrency(string $currency): static
    {
        if (strlen($currency) !== 3) {
            throw new InvalidArgumentException("Invalid currency: $currency. Should be a valid 3 character ISO 4217 code.");
        }

        $this->sepaValues['currency'] = $currency;

        return $this;
    }

    public function setAmount(float $amount): static
    {
        if ($amount < 0.01) {
            throw new InvalidArgumentException("Invalid amount: $amount. Should be minumum 0.01 Euro.");
        }

        if ($amount > 999999999.99) {
            throw new InvalidArgumentException("Invalid amount: $amount. Should be maximum 999999999.99 Euro.");
        }

        $this->sepaValues['amount'] = $amount;

        return $this;
    }

    public function setPurpose(string $purpose): static
    {
        if (strlen($purpose) !== 4) {
            throw new InvalidArgumentException("Invalid purpose code: $purpose. Should be 4 characters.");
        }

        $this->sepaValues['purpose'] = $purpose;

        return $this;
    }

    public function setRemittanceReference(string $remittanceReference): static
    {
        if (strlen($remittanceReference) > 35) {
            throw new InvalidArgumentException("Invalid structured remittance information: $remittanceReference. Should be maximum 35 characters.");
        }

        if (isset($this->sepaValues['remittanceText'])) {
            throw new InvalidArgumentException("Invalid remittance information. Use either structured or unstructured remittance information, not both.");
        }

        $this->sepaValues['remittanceReference'] = (string)$remittanceReference;

        return $this;
    }

    public function setRemittanceText(string $remittanceText): static
    {
        if (strlen($remittanceText) > 140) {
            throw new InvalidArgumentException("Invalid unstructured remittance information: $remittanceText. Should be maximum 140 characters.");
        }

        if (isset($this->sepaValues['remittanceReference'])) {
            throw new InvalidArgumentException("Invalid remittance information. Use either structured or unstructured remittance information, not both.");
        }

        $this->sepaValues['remittanceText'] = $remittanceText;

        return $this;
    }

    public function setInformation(string $information): static
    {
        if (strlen($information) > 70) {
            throw new InvalidArgumentException("Invalid beneficiary to originator information: $information. Should be maximum 70 characters.");
        }

        $this->sepaValues['information'] = $information;

        return $this;
    }

    public function __toString(): string
    {
        $defaults = array(
            'bic' => '',
            'name' => '',
            'iban' => '',
            'currency' => 'EUR',
            'amount' => 0,
            'purpose' => '',
            'remittanceReference' => '',
            'remittanceText' => '',
            'information' => ''
        );

        $values = array_merge($defaults, $this->sepaValues);

        if (!$values['name']) {
            throw new LogicException('Name of the beneficiary is required.');
        }

        if (!$values['iban']) {
            throw new LogicException('Account number of the beneficiary is required.');
        }

        if ($values['version'] === 1 && !$values['bic']) {
            throw new LogicException("BIC of the beneficiary is required.");
        }

        /** @var float */
        $amount = $values['amount'];

        return rtrim(implode("\n", array(
            $values['serviceTag'],
            sprintf('%03d', $values['version']),
            $values['characterSet'],
            $values['identification'],
            $values['bic'],
            $values['name'],
            $values['iban'],
            self::formatMoney($amount),
            $values['purpose'],
            $values['remittanceReference'],
            $values['remittanceText'],
            $values['information']
        )), "\n");
    }
}
