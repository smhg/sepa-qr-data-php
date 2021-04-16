# sepa-qr-php [![CI](https://github.com/smhg/sepa-qr-php/workflows/CI/badge.svg)](https://github.com/smhg/sepa-qr-php/actions)
Generates SEPA QR codes based on the [European Payments Council's standard](http://www.europeanpaymentscouncil.eu/index.cfm/knowledge-bank/epc-documents/quick-response-code-guidelines-to-enable-data-capture-for-the-initiation-of-a-sepa-credit-transfer/epc069-12-quick-response-code-guidelines-to-enable-data-capture-for-the-initiation-of-a-sepa-credit-transfer1/). These QR codes are scannable by many mobile banking apps. Because app support is at a decent level, it is a good idea to print such a code on an invoice.

Extends [endroid/qr-code](https://github.com/endroid/QrCode) preserving all its functionality in case lower level QR code manipulation is needed.

> **PHP 5.6 and <7.1 support:** use version 2.x of this library.

## Installation
```bash
composer require smhg/sepa-qr
```

## Example
```php
use SepaQr\SepaQr;

$sepaQr = new SepaQr();

$sepaQr
  ->setName('Name of the beneficiary')
  ->setIban('BE123456789123456789')
  ->setAmount(100) // The amount in Euro
  ->setRemittanceText('Invoice 123456789')
  ->setSize(300);

// Output to browser:
header('Content-Type: ' . $sepaQr->getContentType());
echo $sepaQr->writeString();

// Or embed as image:
echo '<img src="' . $sepaQr->writeDataUri() . '">';

// Or generate a temporary file:
$tmpFileName = tempnam('/tmp', 'prefix');
$tmpFile = fopen($tmpFileName, 'w');
fwrite($tmpFile, $sepaQr->writeString());
// ... add file to your PDF
fclose($tmpFile);
unlink($tmpFileName);
```
## Methods

### setServiceTag($serviceTag = 'BCD')
Set the service tag. Currently (?) only one value is allowed: BCD.

### setVersion($version = 2)
Set the SEPA QR standard version. In version 1 a BIC is mandatory. In version 2 a BIC is only mandatory outside EEA countries.

### setCharacterSet($characterSet = SepaQr::UTF_8)
Set the character set. Available constants are **UTF_8**, **ISO8859_5**, **ISO8859_1**, **ISO8859_7**, **ISO8859_2**, **ISO8859_10**, **ISO8859_4** or **ISO8859_15**.

### setIdentification($identification = 'SCT')
Set the identification code. Currently (?) only one value is allowed: SCT.

### setBic($bic)
Set the AT-23 BIC of the beneficiary bank.

### setName($name)
Set the AT-21 name of the beneficiary

### setIban($iban)
Set the AT-20 account number of the beneficiary. Only IBAN is allowed.

### setAmount($amount)
Set the AT-04 amount of the credit transfer. Currently (?) only amounts in Euro are allowed.

### setPurpose($purpose)
Set the AT-44 purpose of the credit transfer.

### setRemittanceReference($remittanceReference)
Set the AT-05 remittance information (structured). Creditor reference (ISO 11649) RF creditor reference may be used.

### setRemittanceText($remittanceText)
Set the AT-05 remittance information (unstructured).

### setInformation($information)
Set the beneficiary to originator information.
