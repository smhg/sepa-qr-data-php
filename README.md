# sepa-qr-data [![CI](https://github.com/smhg/sepa-qr-data-php/workflows/CI/badge.svg)](https://github.com/smhg/sepa-qr-data-php/actions)

Generates the text/data to create a SEPA QR code based on the [European Payments Council's standard](http://www.europeanpaymentscouncil.eu/index.cfm/knowledge-bank/epc-documents/quick-response-code-guidelines-to-enable-data-capture-for-the-initiation-of-a-sepa-credit-transfer/epc069-12-quick-response-code-guidelines-to-enable-data-capture-for-the-initiation-of-a-sepa-credit-transfer1/). QR codes using this data are scannable by, for instance, many mobile banking apps and can be used on invoices etc.

> **Migrating from smhg/sepa-qr?** Follow the [steps below](https://github.com/smhg/sepa-qr-data-php#migration-from-smhgsepa-qr).

## Installation
```bash
composer require smhg/sepa-qr-data
```

## Usage

```php
use SepaQr\Data as SepaQrData;

$sepaQrData = (new SepaQrData())
    ->setName('Name of the beneficiary')
    ->setIban('BE123456789123456789')
    ->setAmount(100) // The amount in Euro
;
echo $sepaQrData; // calls $sepaQrData->__toString()
```
This will output:
```
BCD
002
1
SCT

Name of the beneficiary
BE123456789123456789
EUR100.00
```

You can now create a QR code using (for example) [endroid/qr-code](https://github.com/endroid/qr-code):
```php
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelMedium;
use Endroid\QrCode\Writer\PngWriter;

$result = Builder::create()
    ->writer(new PngWriter())
    ->data($sepaQrData) // calls $sepaQrData->__toString()
    ->errorCorrectionLevel(new ErrorCorrectionLevelMedium()) // "medium" is the standard for EPC QR codes, but endroid/qr-code's default is "low"
    ->build();
```
The [endroid/qr-code](https://github.com/endroid/qr-code) project lists the different ways the output (`$result`) can be used.

## Methods

### setServiceTag($serviceTag = 'BCD')
Set the service tag. Currently (?) only one value is allowed: BCD.

### setVersion($version = 2)
Set the SEPA QR standard version. In version 1 a BIC is mandatory. In version 2 a BIC is only mandatory outside EEA countries.

### setCharacterSet($characterSet = SepaQrData::UTF_8)
Set the character set. Available constants are `UTF_8`, `ISO8859_5`, `ISO8859_1`, `ISO8859_7`, `ISO8859_2`, `ISO8859_10`, `ISO8859_4` or `ISO8859_15`.

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

## Migration from smhg/sepa-qr
This project is a continuation of [smhg/sepa-qr](https://github.com/smhg/sepa-qr-php), basically adding PHP8 support. The main difference is the decoupling with the endroid/qr-code library. This project now generates the appropriate QR code data, which can be used with any QR code generating library.

Follow these steps to migrate:

### Remove smhg/sepa-qr
```bash
composer remove smhg/sepa-qr
```

### Install smhg/sepa-qr-data and endroid/qr-code

```bash
composer require smhg/sepa-qr-data endroid/qr-code
```

### Replace use declarations
```diff
-use \SepaQr\SepaQr;
+use \SepaQr\Data as SepaQrData;
+use \Endroid\QrCode\Builder\Builder;
+use \Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelMedium;
+use \Endroid\QrCode\Writer\PngWriter;
```

### Adapt QR code generation accordingly
```php
$sepaQrData = new SepaQrData();
// $sepaQrData->set...

$qrCode = Builder::create()
    ->writer(new PngWriter())
    ->errorCorrectionLevel(new ErrorCorrectionLevelMedium())
    ->data($sepaQrData) // calls $sepaQrData->__toString()
    ->build();

// ... $qrCode->getString() ...
```
