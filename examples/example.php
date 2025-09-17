<?php
/**
 * example.php
 *
 * @created      08.03.2024
 * @author       smiley <smiley@chillerlan.net>
 * @copyright    2024 smiley
 * @license      MIT
 */
declare(strict_types=1);

require_once __DIR__.'/../vendor/autoload.php';

$example = new \chillerlan\LibraryTemplate\Example;

echo $example->hello();
