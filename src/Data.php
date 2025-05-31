<?php declare(strict_types=1);

namespace SepaQr;

use InvalidArgumentException;
use LogicException;

class Data
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

    public static function create(): Data
    {
        return new self();
    }

    public function setServiceTag(string $serviceTag = 'BCD'): static
    {
        if ($serviceTag !== 'BCD') {
            throw new InvalidArgumentException('Invalid service tag');
        }

        $this->sepaValues['serviceTag'] = $serviceTag;

        return $this;
    }

    public function setVersion(int $version = 2): static
    {
        if (!in_array($version, range(1, 2))) {
            throw new InvalidArgumentException('Invalid version');
        }

        $this->sepaValues['version'] = $version;

        return $this;
    }

    public function setCharacterSet(int $characterSet = self::UTF_8): static
    {
        if (!in_array($characterSet, range(1, 8))) {
            throw new InvalidArgumentException('Invalid character set');
        }

        $this->sepaValues['characterSet'] = $characterSet;

        return $this;
    }

    public function setIdentification(string $identification = 'SCT'): static
    {
        if ($identification !== 'SCT') {
            throw new InvalidArgumentException('Invalid identification code');
        }

        $this->sepaValues['identification'] = $identification;

        return $this;
    }

    public function setBic(string $bic): static
    {
        if (strlen($bic) !== 8 && strlen($bic) !== 11) {
            throw new InvalidArgumentException('BIC of the beneficiary can only be 8 or 11 characters');
        }

        $this->sepaValues['bic'] = $bic;

        return $this;
    }

    public function setName(string $name): static
    {
        if (strlen($name) > 70) {
            throw new InvalidArgumentException('Name of the beneficiary cannot be longer than 70 characters');
        }

        $this->sepaValues['name'] = $name;

        return $this;
    }

    public function setIban(string $iban): static
    {
        if (strlen($iban) > 34) {
            throw new InvalidArgumentException('Account number of the beneficiary cannot be longer than 34 characters');
        }

        $this->sepaValues['iban'] = $iban;

        return $this;
    }

    public function setCurrency(string $currency): static
    {
        if (strlen($currency) !== 3) {
            throw new InvalidArgumentException('Currency of the credit transfer can only be a valid ISO 4217 code');
        }

        $this->sepaValues['currency'] = $currency;

        return $this;
    }

    public function setAmount(float $amount): static
    {
        if ($amount < 0.01) {
            throw new InvalidArgumentException('Amount of the credit transfer cannot be smaller than 0.01 Euro');
        }

        if ($amount > 999999999.99) {
            throw new InvalidArgumentException('Amount of the credit transfer cannot be higher than 999999999.99 Euro');
        }

        $this->sepaValues['amount'] = $amount;

        return $this;
    }

    public function setPurpose(string $purpose): static
    {
        if (strlen($purpose) !== 4) {
            throw new InvalidArgumentException('Purpose code can only be 4 characters');
        }

        $this->sepaValues['purpose'] = $purpose;

        return $this;
    }

    public function setRemittanceReference(string $remittanceReference): static
    {
        if (strlen($remittanceReference) > 35) {
            throw new InvalidArgumentException('Structured remittance information cannot be longer than 35 characters');
        }

        if (isset($this->sepaValues['remittanceText'])) {
            throw new InvalidArgumentException('Use either structured or unstructured remittance information');
        }

        $this->sepaValues['remittanceReference'] = (string)$remittanceReference;

        return $this;
    }

    public function setRemittanceText(string $remittanceText): static
    {
        if (strlen($remittanceText) > 140) {
            throw new InvalidArgumentException('Unstructured remittance information cannot be longer than 140 characters');
        }

        if (isset($this->sepaValues['remittanceReference'])) {
            throw new InvalidArgumentException('Use either structured or unstructured remittance information');
        }

        $this->sepaValues['remittanceText'] = $remittanceText;

        return $this;
    }

    public function setInformation(string $information): static
    {
        if (strlen($information) > 70) {
            throw new InvalidArgumentException('Beneficiary to originator information cannot be longer than 70 characters');
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

        if ($values['version'] === 1 && !$values['bic']) {
            throw new LogicException('Missing BIC of the beneficiary bank');
        }

        if (!$values['name']) {
            throw new LogicException('Missing name of the beneficiary');
        }

        if (!$values['iban']) {
            throw new LogicException('Missing account number of the beneficiary');
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
