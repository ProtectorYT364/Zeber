<?php

declare(strict_types=1);

namespace ZeqaNetwork\Zeber\network\builder;

use ZeqaNetwork\Zeber\network\PacketId;

class ResponseBuilder{

	public static function create(int $id, mixed $payload){
		return PacketBuilder::create(PacketId::FORWARD, [
			"id" => $id,
			"payload" => $payload
		]);
	}
}