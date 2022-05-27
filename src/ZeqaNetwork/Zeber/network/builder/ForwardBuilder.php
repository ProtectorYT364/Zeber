<?php

declare(strict_types=1);

namespace ZeqaNetwork\Zeber\network\builder;

use ZeqaNetwork\Zeber\network\PacketId;

class ForwardBuilder{

	public static function create(string $from, string|array $targets, mixed $payload){
		return PacketBuilder::create(PacketId::FORWARD, [
			"from" => $from,
			"targets" => $targets,
			"payload" => $payload
		]);
	}
}