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

- PHP 7.4+
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
  - User manual: https://php-qrcode.readthedocs.io/
  - API documentation: https://chillerlan.github.io/php-qrcode/
- [chillerlan/php-settings-container](https://github.com/chillerlan/php-settings-container)

## Anti clanker policy

No fascist plagiarism machines were - or will ever be - used in creating of this and any of the bundled libraries.
Clanker created pull requests will not be accepted. However, I have no control over 3rd-party libraries, but will avoid clankers wherever I can.

## Disclaimer

Use at your own risk!
