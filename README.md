# sepa-qr-data [![CI](https://github.com/smhg/sepa-qr-data-php/workflows/CI/badge.svg)](https://github.com/smhg/sepa-qr-data-php/actions)
Generates SEPA payment data for use in a QR code as defined in the [European Payments Council's standard](http://www.europeanpaymentscouncil.eu/index.cfm/knowledge-bank/epc-documents/quick-response-code-guidelines-to-enable-data-capture-for-the-initiation-of-a-sepa-credit-transfer/epc069-12-quick-response-code-guidelines-to-enable-data-capture-for-the-initiation-of-a-sepa-credit-transfer1/).

A QR code using this data can, for instance, be displayed on an invoice and be scanned by a mobile banking app.

> **Migrating from smhg/sepa-qr?** Follow the [steps below](https://github.com/smhg/sepa-qr-data-php#migration-from-smhgsepa-qr).

## Installation
```bash
composer require smhg/sepa-qr-data
```

## Usage
```php
use SepaQr\Data;
```

```php
$paymentData = Data::create()
  ->setName('Name of the beneficiary')
  ->setIban('BE123456789123456789')
  ->setAmount(100); // The amount in Euro
```

### With [endroid/qr-code](https://github.com/endroid/qr-code)
#### Installation 
```bash
composer require endroid/qr-code
```

#### Usage
```php
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelMedium;
```

```php
Builder::create()
    ->data($paymentData)
    ->errorCorrectionLevel(new ErrorCorrectionLevelMedium()) // required by EPC standard
    ->build()
    ->saveToFile('payment.png');
```
**Note:** endroid/qr-code lists [more ways](https://github.com/endroid/qr-code#usage-working-with-results) to render.

### With [chillerlan/php-qrcode](https://github.com/chillerlan/php-qrcode)
#### Installation
```bash
composer require chillerlan/php-qrcode
```

#### Usage
```php
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
```

```php
$qrOptions = new QROptions([
    'eccLevel' => QRCode::ECC_M // required by EPC standard
]);

(new QRCode($qrOptions))->render($paymentData, 'payment.png');
```
**Note:** chillerlan/php-qrcode lists [more ways](https://github.com/chillerlan/php-qrcode/wiki/Advanced-usage) to render.

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

#### setCharacterSet($characterSet = Data::UTF_8)
Set the character set. Available constants are `UTF_8`, `ISO8859_5`, `ISO8859_1`, `ISO8859_7`, `ISO8859_2`, `ISO8859_10`, `ISO8859_4` or `ISO8859_15`. Remember to also use/set this character set in the surrounding parts of your application (including endroid/qr-code).

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
```

**4. Adapt QR code generation accordingly**
```php
$paymentData = Data::create();
// ->set...

Builder::create()
    ->errorCorrectionLevel(new ErrorCorrectionLevelMedium())
    ->data($paymentData)
    ->build()
    ->saveToFile('payment.png');
```
