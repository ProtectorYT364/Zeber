<?php

declare(strict_types=1);

namespace ZeqaNetwork\Zeber\network;

use pocketmine\utils\Binary;
use pocketmine\utils\Utils;
use function igbinary_serialize;
use function json_decode;
use function json_last_error_msg;
use function strlen;
use function substr;
use function zlib_decode;

class PacketDecoder extends \AkmalFairuz\Sobana\encoding\PacketDecoder{

	public function decode(string $buffer) : string{
		$offset = 0;

		$ret = [];
		$bufLen = strlen($buffer);
		while($bufLen > $offset){
			if($bufLen < 4){
				break;
			}
			$len = Binary::readInt(substr($buffer, 0, 4));
			if($bufLen < $len){
				break;
			}
			$offset += 4;
			$payload = substr($buffer, $offset, $len);
			$offset += $len;
			Utils::assumeNotFalse($decompressed = zlib_decode($payload));
			Utils::assumeNotFalse($decoded = json_decode($decompressed, true), "Failed to decode json: " . json_last_error_msg());
			$ret[] = $decoded;
		}
		$this->saveBuffer(substr($buffer, $offset));
		if($ret === []){
			return "";
		}
		return igbinary_serialize($ret);
	}
}