<?php
namespace SepaQr;

function formatMoney($value) {
    return sprintf('EUR%s', number_format($value, 2, '.', ''));
}

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

    private $sepaValues = array(
        'serviceTag' => 'BCD',
        'version' => 2,
        'characterSet' => 1,
        'identification' => 'SCT'
    );

    public function setServiceTag(string $serviceTag = 'BCD')
    {
        if ($serviceTag !== 'BCD') {
            throw new Exception('Invalid service tag');
        }

        $this->sepaValues['serviceTag'] = $serviceTag;

        return $this;
    }

    public function setVersion(int $version = 2)
    {
        if (!in_array($version, range(1, 2))) {
            throw new Exception('Invalid version');
        }

        $this->sepaValues['version'] = $version;

        return $this;
    }

    public function setCharacterSet($characterSet = self::UTF_8)
    {
        $characterSet = (int)$characterSet;

        if (!in_array($characterSet, range(1, 8))) {
            throw new Exception('Invalid character set');
        }

        $this->sepaValues['characterSet'] = $characterSet;

        return $this;
    }

    public function setIdentification($identification = 'SCT')
    {
        if ($identification !== 'SCT') {
            throw new Exception('Invalid identification code');
        }

        $this->sepaValues['identification'] = $identification;

        return $this;
    }

    public function setBic($bic)
    {
        if (strlen($bic) < 8) {
            throw new Exception('BIC of the beneficiary bank cannot be shorter than 8 characters');
        }

        if (strlen($bic) > 11) {
            throw new Exception('BIC of the beneficiary bank cannot be longer than 11 characters');
        }

        $this->sepaValues['bic'] = $bic;
        return $this;
    }

    public function setName($name)
    {
        if (strlen($name) > 70) {
            throw new Exception('Name of the beneficiary cannot be longer than 70 characters');
        }

        $this->sepaValues['name'] = $name;

        return $this;
    }

    public function setIban($iban)
    {
        if (strlen($iban) > 34) {
            throw new Exception('Account number of the beneficiary cannot be longer than 34 characters');
        }

        $this->sepaValues['iban'] = $iban;

        return $this;
    }

    public function setAmount($amount)
    {
        if ($amount < 0.01) {
            throw new Exception('Amount of the credit transfer cannot be smaller than 0.01 Euro');
        }

        if ($amount > 999999999.99) {
            throw new Exception('Amount of the credit transfer cannot be higher than 999999999.99 Euro');
        }

        $this->sepaValues['amount'] = (float)$amount;

        return $this;
    }

    public function setPurpose($purpose)
    {
        $this->sepaValues['purpose'] = $purpose;

        return $this;
    }

    public function setRemittanceReference($remittanceReference)
    {
        if (strlen($remittanceReference) > 35) {
            throw new Exception('Structured remittance information cannot be longer than 35 characters');
        }

        if (isset($this->sepaValues['remittanceText'])) {
            throw new Exception('Use either structured or unstructured remittance information');
        }

        $this->sepaValues['remittanceReference'] = (string)$remittanceReference;

        return $this;
    }

    public function setRemittanceText($remittanceText)
    {
        if (strlen($remittanceText) > 140) {
            throw new Exception('Unstructured remittance information cannot be longer than 140 characters');
        }

        if (isset($this->sepaValues['remittanceReference'])) {
            throw new Exception('Use either structured or unstructured remittance information');
        }

        $this->sepaValues['remittanceText'] = $remittanceText;

        return $this;
    }

    public function setInformation($information)
    {
        if (strlen($information) > 70) {
            throw new Exception('Beneficiary to originator information cannot be longer than 70 characters');
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
            'amount' => 0,
            'purpose' => '',
            'remittanceReference' => '',
            'remittanceText' => '',
            'information' => ''
        );

        $values = array_merge($defaults, $this->sepaValues);

        if ($values['version'] === 1 && !$values['bic']) {
            throw new Exception('Missing BIC of the beneficiary bank');
        }

        if (!$values['name']) {
            throw new Exception('Missing name of the beneficiary');
        }

        if (!$values['iban']) {
            throw new Exception('Missing account number of the beneficiary');
        }

        return rtrim(implode("\n", array(
            $values['serviceTag'],
            sprintf('%03d', $values['version']),
            $values['characterSet'],
            $values['identification'],
            $values['bic'],
            $values['name'],
            $values['iban'],
            $values['amount'] > 0 ? formatMoney($values['amount']) : '',
            $values['purpose'],
            $values['remittanceReference'],
            $values['remittanceText'],
            $values['information']
        )), "\n");
    }
}
