<?php
/**
 * Class TwoFactorQRCodeOptions
 *
 * @created      17.09.2025
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2025 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace chillerlan\TwoFactorQRCode;

use chillerlan\Authenticator\AuthenticatorOptionsTrait;
use chillerlan\QRCode\QRCodeReaderOptionsTrait;
use chillerlan\QRCode\QROptionsTrait;
use chillerlan\Settings\SettingsContainerAbstract;

class TwoFactorQRCodeOptions extends SettingsContainerAbstract{
	use AuthenticatorOptionsTrait, QROptionsTrait, QRCodeReaderOptionsTrait;
}
