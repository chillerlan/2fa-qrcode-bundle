# chillerlan/2fa-qrcode-bundle

An authenticator ([chillerlan/php-authenticator](https://github.com/chillerlan/php-authenticator)) and a QR Code generator ([chillerlan/php-qrcode](https://github.com/chillerlan/php-qrcode)) bundled together for MFA in frameworks and applications.

[![PHP Version Support][php-badge]][php]
[![Packagist version][packagist-badge]][packagist]
[![License][license-badge]][license]
[![Continuous Integration][gh-action-badge]][gh-action]
[![Packagist downloads][downloads-badge]][downloads]

[php-badge]: https://img.shields.io/packagist/php-v/chillerlan/2fa-qrcode-bundle?logo=php&color=8892BF&logoColor=fff
[php]: https://www.php.net/supported-versions.php
[packagist-badge]: https://img.shields.io/packagist/v/chillerlan/2fa-qrcode-bundle.svg?logo=packagist&logoColor=fff
[packagist]: https://packagist.org/packages/chillerlan/2fa-qrcode-bundle
[license-badge]: https://img.shields.io/github/license/chillerlan/2fa-qrcode-bundle
[license]: https://github.com/chillerlan/2fa-qrcode-bundle/blob/main/LICENSE
[gh-action-badge]: https://img.shields.io/github/actions/workflow/status/chillerlan/2fa-qrcode-bundle/ci.yml?branch=main&logo=github&logoColor=fff
[gh-action]: https://github.com/chillerlan/2fa-qrcode-bundle/actions/workflows/ci.yml?query=branch%3Amain
[downloads-badge]: https://img.shields.io/packagist/dt/chillerlan/2fa-qrcode-bundle.svg?logo=packagist&logoColor=fff
[downloads]: https://packagist.org/packages/chillerlan/2fa-qrcode-bundle/stats

## Requirements

- PHP 8.2+
  - [`ext-mbstring`](https://www.php.net/manual/book.mbstring.php)
  - optional:
    - [`ext-gd`](https://www.php.net/manual/book.image) for `QRGdImage` based output
    - [`ext-imagick`](https://github.com/Imagick/imagick) with [ImageMagick](https://imagemagick.org) installed
    - [`ext-fileinfo`](https://www.php.net/manual/book.fileinfo.php) required by `QRImagick` output
    - optional libraries, see the [php-qrcode requirements](https://github.com/chillerlan/php-qrcode)

## Documentation

You can find the documentation of the bundled libraries in their respective repositories:

- [chillerlan/php-authenticator](https://github.com/chillerlan/php-authenticator)
- [chillerlan/php-qrcode](https://github.com/chillerlan/php-qrcode)
  - User manual: https://php-qrcode.readthedocs.io/en/v6.0.x/
  - API documentation: https://chillerlan.github.io/php-qrcode/
- [chillerlan/php-settings-container](https://github.com/chillerlan/php-settings-container)

### User Guide

#### Invocation

Fetch the settings from e.g. a framework config and [invoke the `TwoFactorQRCodeOptions` instance](https://php-qrcode.readthedocs.io/en/v6.0.x/Usage/Advanced-usage.html#configuration-via-qroptions) with it.
Please note that this object combines the settings for [`AuthenticatorOptions`](https://github.com/chillerlan/php-authenticator?tab=readme-ov-file#authenticatoroptions) and [`QROptions`](https://php-qrcode.readthedocs.io/en/v6.0.x/Usage/Configuration-settings.html).
Alternatively, you can just pass an `iterable` of options to the `TwoFactorQRCode` constructor.

```php
use chillerlan\TwoFactorQRCode\TwoFactorQRCode;

$options = [
	'secret_length' => 128,
	'adjacent'      => 2,
];

$twoFactorQRCode = new TwoFactorQRCode($options);
```

#### User registration

- Create a secret, present it to the user as text.
- Create a QR Code and present it to the user.
- Generate a backup code and present it as well, save the counter value.
- Let the user create a fresh OTP with the new credentials.
- Save the data on successful verification and let the user triple check if they saved their backup code.

```php
$newSecret = $twoFactorQRCode->createSecret();
$qrcode    = $twoFactorQRCode->getQRCode('label', 'issuer');
$backup    = $twoFactorQRCode->createBackupCode($counterValue);

if($twoFactorQRCode->verifyOTP($newOTP)){
	echo 'yay! do not forget to save your backup code!';
}
```

#### Normal usage: log-ins, reverification etc.

- Set the secret from the user's data.
- Present the user with a form field for the OTP and verify it.
- Verify the log-in and redirect to wherever the user was headed.

```php
$twoFactorQRCode->setSecret($userSecret);

if($twoFactorQRCode->verifyOTP($OTP)){
	// do stuff...

	// redirect
	header('Location: ...');
}
```

#### Using a backup code

The user has lost access to their authenticator, send them to a form separate from the usual OTP input:

- Verify the given backup OTP against the stored counter value.
- After verification, increase the counter, create a new backup code and save the new counter value.
- Present the new backup code to the user and make them triple check that they have carefully saved it.

```php
$twoFactorQRCode->setSecret($userSecret);

if($twoFactorQRCode->verifyBackupCode($backupOTP, $counterValue)){
	$counterValue++;

	$newBackup = $twoFactorQRCode->createBackupCode($counterValue);

	// redirect
}
```

Redirect the user to wherever they can manage their 2FA settings and retrieve their secret once again:

```php
$twoFactorQRCode->setSecret($userSecret);

$currentBackup = $twoFactorQRCode->createBackupCode($currentCounterValue);
$qrcode        = $twoFactorQRCode->getQRCode('label', 'issuer');
```

#### Validating an e-mail address

The previous flow can also be used for other tasks, such as e-mail verification:

- Create a temporary secret and random counter value.
- Create a verification code from the above data.
- Store the temporary secret and counter together with the email address and the current timestamp.
- Send an e-mail with the code to the given e-mail address.

```php
$tempSecret       = $twoFactorQRCode->createSecret();
$tempCounter      = random_int(1, 999999);
$verificationCode = $twoFactorQRCode->createBackupCode($counterValue);
```

Now present the user with a form where they can enter the received code:

- Fetch the temporary counter and secret from the storage.
- Verify the code.
- Delete the temporary data after successful verification (or after a defined duration).

```php
$twoFactorQRCode->setSecret($tempSecret);

if($twoFactorQRCode->verifyBackupCode($verificationCode, $tempCounter)){
	// success!
}
```

### A note on the secret length

The secret length as per the specifications (RFCs [4226](https://tools.ietf.org/html/rfc4226) and [6238](https://tools.ietf.org/html/rfc6238)) is the length of the binary string that is given to the HMAC hash function - there is no encoding involved at all.
Google's "[Key URI format](https://github.com/google/google-authenticator/wiki/Key-Uri-Format)" specification uses base32 encoding in order to make the binary secret string portable (URI safe). The base32 encoding naturally results in longer strings than the original, about 60%, so a secret of 20 bytes length results in a 32 byte long base32 encoded string.

However, some of [the top used libraries on packagist](https://packagist.org/search/?tags=totp) use some kind of pseudo base32 encoding, with a shorter secret string than requested as a result (secret length = base32 encoded length), which can compromise the security of the algorithm.

**This library refers to the secret length always as the length of the raw binary string.**

### API

#### `TwoFactorQRCode`

The class is not final and you're free to extend it to add/change functionality to your liking.

| method                                                                                                             | return   | description                                                                                                   |
|--------------------------------------------------------------------------------------------------------------------|----------|---------------------------------------------------------------------------------------------------------------|
| `__construct(SettingsContainerInterface\|TwoFactorQRCodeOptions \|iterable $options = new TwoFactorQRCodeOptions)` | -        |                                                                                                               |
| `createSecret(int\|null $length = null)`                                                                           | `string` | Creates a cryptograpically secure random secret and returns it as Base32 encoded string                       |
| `setSecret(string $encodedSecret)`                                                                                 | `static` | Sets a secret phrase from an encoded representation                                                           |
| `getSecret()`                                                                                                      | `string` | Returns an encoded representation of the current secret phrase                                                |
| `setRawSecret(string $rawSecret)`                                                                                  | `static` | Sets a secret phrase from a raw binary representation                                                         |
| `getRawSecret()`                                                                                                   | `string` | Returns the raw representation of the current secret phrase                                                   |
| `verifyOTP(string $otp, int\|null $timestamp = null)`                                                              | `bool`   | Verifies a one-time password (TOTP) with an optional unix timestamp                                           |
| `createBackupCode(int $counter)`                                                                                   | `string` | Creates a counter-based one-time password (HOTP) from the given counter value                                 |
| `verifyBackupCode(string $otp, int $counter)`                                                                      | `bool`   | Verifies a counter-based backup code (HOTP) against the given counter value                                   |
| `getQRCode(string $label, string $issuer)`                                                                         | `string` | Creates a QR Code for use with a mobile authenticator application (TOTP) with the given label and issuer name |


## Anti clanker policy

No fascist plagiarism machines were - or will ever be - used in creating of this and any of the bundled libraries.
Clanker created pull requests will not be accepted. However, I have no control over 3rd-party libraries, but will avoid clankers wherever I can.

## Disclaimer

Use at your own risk!
