<?php
/**
 * example.php
 *
 * @created      15.03.2026
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2026 smiley
 * @license      MIT
 */
declare(strict_types=1);

use chillerlan\TwoFactorQRCode\TwoFactorQRCode;
use chillerlan\TwoFactorQRCode\TwoFactorQRCodeOptions;

require_once __DIR__.'/../vendor/autoload.php';

/**
 * invocation
 */

// an options array, e.g. populated from framework config
$options = [
	'secret_length' => 128,
	'adjacent'      => 2,
];

// invoke settings and TwoFactorQRCode instance
$twoFactorQRCodeOptions = new TwoFactorQRCodeOptions($options);
$twoFactorQRCode        = new TwoFactorQRCode($twoFactorQRCodeOptions);


/**
 * installation
 */

// create a secret, present it to the user as text
$newSecret = $twoFactorQRCode->createSecret();

// also create a QR Code and present it to the user
$qrcode    = $twoFactorQRCode->getQRCode('label', 'issuer');

// generate a backup code and present it as well, save the counter value
$backup    = $twoFactorQRCode->createBackupCode(0);

// during the registration, let the user supply an OTP with the above data and verify it
if($twoFactorQRCode->verifyOTP('069420')){
	// proceed with installation, save data
	echo 'yay!';
}


/**
 * normal usage, log-ins, reverification etc.
 */

// set the secret from the user's data
$twoFactorQRCode->setSecret('GEZDGNBVGY3TQOJQGEZDGNBVGY3TQOJQ');

// present the user with a form field for the OTP and verify it
if($twoFactorQRCode->verifyOTP('420069')){
	// redirect to wherever the user was headed
}


/**
 * using a backup code
 */

// the user has lost access to their authenticator, send them to a form separate from the usual OTP input
// verify the given OTP against the stored counter value
if($twoFactorQRCode->verifyBackupCode('694711', 0)){
	// after verification, create a new backup code and save the new counter value
	$newBackup = $twoFactorQRCode->createBackupCode(1);
	// present the new backup code to the user and make them triple check that they have carefully saved it,
	// then redirect them to wherever they were headed
}


/**
 * utility
 */

// alternatively you can set the secret as raw binary, e.g. for migration
$twoFactorQRCode->setRawSecret('decoded binary string');

// you can then retrieve the properly encoded secret
$secret = $twoFactorQRCode->getSecret();

// you can also retrieve the raw secret
$rawSecert = $twoFactorQRCode->getRawSecret();
