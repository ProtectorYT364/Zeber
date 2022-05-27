<?php

declare(strict_types=1);

namespace ZeqaNetwork\Zeber;

use pocketmine\plugin\PluginBase;
use ZeqaNetwork\Zeber\network\PacketDecoder;
use ZeqaNetwork\Zeber\network\PacketEncoder;
use ZeqaNetwork\Zeber\network\ZeberNetSession;
use ZeqaNetwork\Zeber\network\ZeberServerManager;

class Zeber extends PluginBase{

	private static self $instance;

	public static function getInstance() : self{
		return self::$instance;
	}

	public function onEnable() : void{
		self::$instance = $this;

		$cfg = $this->getConfig();

		$server = new ZeberServerManager(
			$cfg->get("listen_address", "0.0.0.0"),
			(int) $cfg->get("listen_port", 5770),
			ZeberNetSession::class,
			PacketEncoder::class,
			PacketDecoder::class
		);
		$server->start();
	}
}