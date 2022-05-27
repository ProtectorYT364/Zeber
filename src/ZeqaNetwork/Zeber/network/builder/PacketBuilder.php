<?php

declare(strict_types=1);

namespace ZeqaNetwork\Zeber\network\builder;

class PacketBuilder{

	public static function create(string $id, mixed $data){
		return [
			"id" => $id,
			"data" => $data
		];
	}
}