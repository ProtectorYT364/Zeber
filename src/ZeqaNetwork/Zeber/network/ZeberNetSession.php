<?php

declare(strict_types=1);

namespace ZeqaNetwork\Zeber\network;

use AkmalFairuz\Sobana\server\ServerSession;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use pocketmine\utils\Utils;
use ZeqaNetwork\Zeber\client\Client;
use ZeqaNetwork\Zeber\client\ClientManager;
use ZeqaNetwork\Zeber\network\builder\PacketBuilder;
use ZeqaNetwork\Zeber\network\types\LoginInfo;
use function igbinary_unserialize;
use function json_encode;
use function json_last_error_msg;

class ZeberNetSession extends ServerSession{

	private Client $client;
	private bool $authenticated = false;

	public function onConnect() : void{

	}

	public function sendPacket(array $packet){
		$this->write(Utils::assumeNotFalse(json_encode($packet), "Failed to encode JSON: " . json_last_error_msg()));
	}

	public function handlePacket(string $packet) : void{
		foreach(igbinary_unserialize($packet) as $p){
			$id = $p["id"];
			$data = $p["data"];
			if($this->authenticated){
				$this->client->handlePacket($id, $data);
			}else{
				switch($id){
					case PacketId::LOGIN:
						$loginInfo = LoginInfo::create($data);
						$name = $loginInfo->name;
						if(ClientManager::getByName($name) !== null){
							$this->sendPacket(PacketBuilder::create(PacketId::AUTH, false));
							$this->close();
							break;
						}
						$this->sendPacket(PacketBuilder::create(PacketId::AUTH, true));
						ClientManager::add($this->client = new Client($this, $this->getId(), $name, $parent = $loginInfo->parent, $loginInfo->type));
						$this->authenticated = true;
						Server::getInstance()->getLogger()->info(TextFormat::GREEN . "Authenticated from $name (parent: $parent) " . TextFormat::GRAY . $this->getIp() . ":" . $this->getPort());
						break;
				}
			}
		}
	}

	public function onClose() : void{
		if(isset($this->client)){
			ClientManager::remove($this->client);
		}
	}
}