<?php

declare(strict_types=1);

namespace ZeqaNetwork\Zeber\network\types;

class LoginInfo{

	const TYPE_SERVER = 0;
	const TYPE_PROXY = 1;

	public function __construct(
		public string $name,
		public string $parent,
		public int $type
	){
	}

	public static function create(array $data){
		return new self($data["name"], $data["parent"], (int) $data["type"]);
	}
}