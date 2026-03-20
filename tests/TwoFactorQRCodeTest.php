<?php
/**
 * Class TwoFactorQRCodeTest
 *
 * @created      24.11.2025
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2025 smiley
 * @license      MIT
 */
declare(strict_types=1);

namespace chillerlan\TwoFactorQRCodeTest;

use chillerlan\Authenticator\Common\Base32;
use chillerlan\QRCode\Common\GDLuminanceSource;
use chillerlan\QRCode\Decoder\Decoder;
use chillerlan\QRCode\Output\QRGdImagePNG;
use chillerlan\TwoFactorQRCode\TwoFactorQRCode;
use chillerlan\TwoFactorQRCode\TwoFactorQRCodeOptions;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use function date;
use function is_int;
use function sprintf;
use const PHP_INT_SIZE;

class TwoFactorQRCodeTest extends TestCase{

	/**
	 * @see https://datatracker.ietf.org/doc/html/rfc6238#appendix-B
	 */
	final protected const string secret = 'GEZDGNBVGY3TQOJQGEZDGNBVGY3TQOJQ';

	/**
	 * @see https://tools.ietf.org/html/rfc4226#page-32
	 */
	final protected const array rfc4226Vectors = [
		[0, '755224'],
		[1, '287082'],
		[2, '359152'],
		[3, '969429'],
		[4, '338314'],
		[5, '254676'],
		[6, '287922'],
		[7, '162583'],
		[8, '399871'],
		[9, '520489'],
	];

	/**
	 * @see https://datatracker.ietf.org/doc/html/rfc6238#appendix-B
	 */
	final protected const array rfc6238Vectors = [
		[         59, '94287082'],
		[ 1111111109, '07081804'],
		[ 1111111111, '14050471'],
		[ 1234567890, '89005924'],
		[ 2000000000, '69279037'],
		[20000000000, '65353130'], // 64bit only
	];


	protected TwoFactorQRCode        $twoFactorQRCode;
	protected TwoFactorQRCodeOptions $twoFactorQRCodeOptions;

	protected function setUp():void{
		$this->twoFactorQRCodeOptions = new TwoFactorQRCodeOptions;
		$this->twoFactorQRCode        = new TwoFactorQRCode($this->twoFactorQRCodeOptions);

		$this->twoFactorQRCode->setSecret(self::secret);
	}

	public function testCreateSecret():void{
		$this::assertMatchesRegularExpression('/^['.Base32::CHARSET.']+$/', $this->twoFactorQRCode->createSecret());
	}

	public function testSetRawSecret():void{
		$rawSecret = Base32::decode(self::secret);

		$this->twoFactorQRCode->setRawSecret($rawSecret);

		$this::assertSame($rawSecret, $this->twoFactorQRCode->getRawSecret());
		$this::assertSame(self::secret, $this->twoFactorQRCode->getSecret());
	}

	public static function totpVectors():Generator{
		foreach(self::rfc6238Vectors as [$timestamp, $totp]){
			// skip 64bit numbers on 32bit PHP
			if(PHP_INT_SIZE < 8 && !is_int($timestamp)){
				continue;
			}

			yield date('Y-m-d H:i:s', $timestamp) => [$timestamp, $totp];
		}
	}

	#[DataProvider('totpVectors')]
	public function testVerifyOTP(int $timestamp, string $totp):void{
		$this->twoFactorQRCodeOptions->digits = 8;

		$this::assertTrue($this->twoFactorQRCode->verifyOTP($totp, $timestamp));
	}

	public static function hotpVectors():Generator{
		foreach(self::rfc4226Vectors as [$counter, $hotp]){
			yield sprintf('value: %d', $counter) => [$counter, $hotp];
		}
	}

	#[DataProvider('hotpVectors')]
	public function testCreateBackupCode(int $counter, string $hotp):void{
		$this::assertSame($hotp, $this->twoFactorQRCode->createBackupCode($counter));
	}

	#[DataProvider('hotpVectors')]
	public function testVerifyBackupCode(int $counter, string $hotp):void{
		$this::assertTrue($this->twoFactorQRCode->verifyBackupCode($hotp, $counter));
	}

	public function testGetQRCode():void{
		/** @phan-suppress-next-line PhanDeprecatedClassConstant */
		$this->twoFactorQRCodeOptions->outputInterface = QRGdImagePNG::class;
		$this->twoFactorQRCodeOptions->outputBase64    = false;

		$qrcode = $this->twoFactorQRCode->getQRCode('testLabel', 'testIssuer');
		$result = new Decoder()->decode(GDLuminanceSource::fromBlob($qrcode));

		$this::assertSame('otpauth://totp/testLabel?secret=GEZDGNBVGY3TQOJQGEZDGNBVGY3TQOJQ&issuer=testIssuer', $result->data);
	}

}
