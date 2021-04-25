# sepa-qr-data [![CI](https://github.com/smhg/sepa-qr-data-php/workflows/CI/badge.svg)](https://github.com/smhg/sepa-qr-data-php/actions)
Generates SEPA payment data for use in a QR code as defined in the [European Payments Council's standard](http://www.europeanpaymentscouncil.eu/index.cfm/knowledge-bank/epc-documents/quick-response-code-guidelines-to-enable-data-capture-for-the-initiation-of-a-sepa-credit-transfer/epc069-12-quick-response-code-guidelines-to-enable-data-capture-for-the-initiation-of-a-sepa-credit-transfer1/).

A QR code using this data can, for instance, be displayed on an invoice and be scanned by a mobile banking app.

> **Migrating from smhg/sepa-qr?** Follow the [steps below](https://github.com/smhg/sepa-qr-data-php#migration-from-smhgsepa-qr).

## Installation
```bash
composer require smhg/sepa-qr-data
```
You'll probably also want to install a QR code library like [endroid/qr-code](https://github.com/endroid/qr-code):
```bash
composer require endroid/qr-code
```

## Usage
### Using endroid/qr-code
```php
use SepaQr\Data;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelMedium;
use Endroid\QrCode\Writer\PngWriter;
```

```php
// create payment data
$paymentData = Data::create()
  ->setName('Name of the beneficiary')
  ->setIban('BE123456789123456789')
  ->setAmount(100); // The amount in Euro

// create QR code
$qrCode = Builder::create()
    ->data($paymentData)
    ->errorCorrectionLevel(new ErrorCorrectionLevelMedium())
    ->build();

// save as image
$qrCode->saveToFile('qrcode.png');

```
**Note:** [endroid/qr-code](https://github.com/endroid/qr-code) lists more ways to use the output (`$qrCode`).

## API

#### setName($name)
**Required.** Set the name of the beneficiary.

#### setIban($iban)
**Required.** Set the account number of the beneficiary. Only IBAN is allowed.

#### setAmount($amount)
Set the amount of the credit transfer. Currently (?) only amounts in Euro are allowed.

#### setBic($bic)
Set the BIC of the beneficiary bank.

#### setRemittanceReference($remittanceReference)
Set the remittance information (structured). Creditor reference (ISO 11649) RF creditor reference may be used.

#### setRemittanceText($remittanceText)
Set the remittance information (unstructured).

#### setPurpose($purpose)
Set the purpose of the credit transfer.

#### setInformation($information)
Set the beneficiary to originator information.

#### setServiceTag($serviceTag = 'BCD')
Set the service tag. Currently (?) only one value is allowed: BCD.

#### setVersion($version = 2)
Set the SEPA QR standard version. In version 1 a BIC is mandatory. In version 2 a BIC is only mandatory outside EEA countries.

#### setCharacterSet($characterSet = SepaQrData::UTF_8)
Set the character set. Available constants are `UTF_8`, `ISO8859_5`, `ISO8859_1`, `ISO8859_7`, `ISO8859_2`, `ISO8859_10`, `ISO8859_4` or `ISO8859_15`.

#### setIdentification($identification = 'SCT')
Set the identification code. Currently (?) only one value is allowed: SCT.

## Migration from smhg/sepa-qr
This project is a continuation of [smhg/sepa-qr](https://github.com/smhg/sepa-qr-php), decoupling QR code rendering. Different QR rendering libraries offer different features and support different PHP versions. This project now generates the appropriate QR code data, which can be supplied to the QR code rendering library of your choice.

Follow these steps to migrate:

**1. Remove smhg/sepa-qr**
```bash
composer remove smhg/sepa-qr
```

**2. Install smhg/sepa-qr-data and endroid/qr-code**

```bash
composer require smhg/sepa-qr-data endroid/qr-code
```

**3. Replace/add use declarations**
```diff
-use \SepaQr\SepaQr;
+use \SepaQr\Data;
+use \Endroid\QrCode\Builder\Builder;
+use \Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelMedium;
+use \Endroid\QrCode\Writer\PngWriter;
```

**4. Adapt QR code generation accordingly**
```php
$paymentData = Data::create();
// ->set...

$qrCode = Builder::create()
    ->errorCorrectionLevel(new ErrorCorrectionLevelMedium())
    ->data($paymentData)
    ->build();

// ... $qrCode->getString() ...
```
