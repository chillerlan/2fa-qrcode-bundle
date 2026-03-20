<?php
/**
 * Class TwoFactorQRCode
 *
 * @created      17.09.2025
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2025 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace chillerlan\TwoFactorQRCode;

use chillerlan\QRCode\QRCode;
use chillerlan\Authenticator\Authenticators\{HOTP, TOTP};
use chillerlan\Settings\SettingsContainerInterface;

class TwoFactorQRCode{

	protected SettingsContainerInterface $options;
	protected HOTP                       $hotp;
	protected TOTP                       $totp;

	/**
	 * @param \chillerlan\Settings\SettingsContainerInterface|\chillerlan\TwoFactorQRCode\TwoFactorQRCodeOptions|null $options
	 */
	public function __construct(?SettingsContainerInterface $options = null){
		$this->options = ($options ?? new TwoFactorQRCodeOptions);
		$this->hotp    = new HOTP($this->options);
		$this->totp    = new TOTP($this->options);
	}

	/**
	 * Creates a cryptographically secure random secret and returns it as Base32 encoded string.
	 *
	 * Note: The secret length is the length of the raw binary string,
	 *       the Base32 encoded string is considerably longer (~60%).
	 *       e.g. 20 bytes raw => 32 bytes Base32
	 *
	 * @see \random_bytes()
	 * @see \ParagonIE\ConstantTime\Base32
	 */
	public function createSecret(?int $length = null):string{
		$secret = $this->hotp->createSecret($length);

		$this->totp->setSecret($secret);

		return $secret;
	}

	/**
	 * Sets a secret phrase from an encoded representation
	 */
	public function setSecret(string $encodedSecret):self{
		$this->hotp->setSecret($encodedSecret);
		$this->totp->setSecret($encodedSecret);

		return $this;
	}

	/**
	 * Returns an encoded representation of the current secret phrase
	 */
	public function getSecret():string{
		return $this->hotp->getSecret();
	}

	/**
	 * Sets a secret phrase from a raw binary representation
	 */
	public function setRawSecret(string $rawSecret):self{
		$this->hotp->setRawSecret($rawSecret);
		$this->totp->setRawSecret($rawSecret);

		return $this;
	}

	/**
	 * Returns the raw representation of the current secret phrase
	 */
	public function getRawSecret():string{
		return $this->hotp->getRawSecret();
	}

	/**
	 * Verifies a one-time password (TOTP) with an optional unix timestamp.
	 *
	 * @see \chillerlan\Authenticator\Authenticators\TOTP
	 */
	public function verifyOTP(string $otp, ?int $timestamp = null):bool{
		return $this->totp->verify($otp, $timestamp);
	}

	/**
	 * Creates a counter-based one-time password (HOTP) from the given counter value.
	 *
	 * @see \chillerlan\Authenticator\Authenticators\HOTP
	 */
	public function createBackupCode(int $counter):string{
		return $this->hotp->code($counter);
	}

	/**
	 * Verifies a counter-based backup code (HOTP) against the given counter value.
	 *
	 * @see \chillerlan\Authenticator\Authenticators\HOTP
	 */
	public function verifyBackupCode(string $otp, int $counter):bool{
		return $this->hotp->verify($otp, $counter);
	}

	/**
	 * Creates a QR Code for use with a mobile authenticator application (TOTP) with the given label and issuer name.
	 *
	 * @see \chillerlan\QRCode\QRCode
	 * @see https://php-qrcode.readthedocs.io/
	 */
	public function getQRCode(string $label, string $issuer):string{
		$uri = $this->totp->getUri($label, $issuer);

		return (new QRCode($this->options))->render($uri);
	}

}
