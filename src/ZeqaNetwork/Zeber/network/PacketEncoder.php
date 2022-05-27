<?php

declare(strict_types=1);

namespace ZeqaNetwork\Zeber\network;

use pocketmine\utils\Binary;
use function strlen;
use function zlib_encode;
use const ZLIB_ENCODING_RAW;

class PacketEncoder extends \AkmalFairuz\Sobana\encoding\PacketEncoder{

	const COMPRESSION_LEVEL = 6;

	public function encode(string $buffer) : string{
		$compress = zlib_encode($buffer, ZLIB_ENCODING_RAW, self::COMPRESSION_LEVEL);
		return Binary::writeInt(strlen($compress)) . $compress;
	}
}