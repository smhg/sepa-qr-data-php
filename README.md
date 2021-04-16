# sepa-qr-php [![CI](https://github.com/smhg/sepa-qr-data-php/workflows/CI/badge.svg)](https://github.com/smhg/sepa-qr-data-php/actions)
Generates SEPA QR code data based on the [European Payments Council's standard](http://www.europeanpaymentscouncil.eu/index.cfm/knowledge-bank/epc-documents/quick-response-code-guidelines-to-enable-data-capture-for-the-initiation-of-a-sepa-credit-transfer/epc069-12-quick-response-code-guidelines-to-enable-data-capture-for-the-initiation-of-a-sepa-credit-transfer1/). QR codes using this data are, for instance, scannable by many mobile banking apps and can be used on invoices etc.

## Installation
```bash
composer require smhg/sepa-qr-data
```
You'll probably also want to install a QR code library like [endroid/qr-code](https://github.com/endroid/qr-code).

## Example using endroid/qr-code
```php
use SepaQr\Data as SepaQrData;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelMedium;
use Endroid\QrCode\Writer\PngWriter;

$sepaQrData = new SepaQrData();

$sepaQrData
  ->setName('Name of the beneficiary')
  ->setIban('BE123456789123456789')
  ->setAmount(100) // The amount in Euro
  ->setRemittanceText('Invoice 123456789');

$result = Builder::create()
    ->writer(new PngWriter())
    ->writerOptions([])
    ->data($sepaQrData) // calls $sepaQrData->__toString()
    ->errorCorrectionLevel(new ErrorCorrectionLevelMedium())
    ->build();
```
The [endroid/qr-code](https://github.com/endroid/qr-code) project contains information on how to use output (`$result`).

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
