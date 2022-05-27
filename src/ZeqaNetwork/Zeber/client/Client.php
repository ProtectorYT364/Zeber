<?php

declare(strict_types=1);

namespace ZeqaNetwork\Zeber\client;

use ZeqaNetwork\Zeber\network\builder\PacketBuilder;
use ZeqaNetwork\Zeber\network\builder\ResponseBuilder;
use ZeqaNetwork\Zeber\network\PacketId;
use ZeqaNetwork\Zeber\network\types\LoginInfo;
use ZeqaNetwork\Zeber\network\ZeberNetSession;
use function array_filter;
use function array_map;
use function count;
use function is_string;

class Client{

	public function __construct(
		private ZeberNetSession $session,
		private int $id,
		private string $name,
		private string $parent,
		private int $type
	){
	}

	public function getNetSession() : ZeberNetSession{
		return $this->session;
	}

	public function getId() : int{
		return $this->id;
	}

	public function getName() : string{
		return $this->name;
	}

	public function getParent() : string{
		return $this->parent;
	}

	public function getType() : int{
		return $this->type;
	}

	public function isServer() : bool{
		return $this->type === LoginInfo::TYPE_SERVER;
	}

	public function isProxy() : bool{
		return $this->type === LoginInfo::TYPE_PROXY;
	}

	public function handlePacket(string $id, mixed $data){
		switch($id){
			case PacketId::FORWARD:
				$this->handleForward($data);
				break;
			case PacketId::REQUEST:
				$this->handleRequest($data);
				break;
		}
	}

	public function sendPacket(array $packet){
		$this->session->sendPacket($packet);
	}

	private function handleForward(array $payload){
		$targets = $payload["targets"];
		if(is_string($targets)){
			$targets = match ($targets) {
				"all_clients" => ClientManager::getAll(),
				"others_clients" => array_filter(ClientManager::getAll(), function(?Client $client) : bool{
					return $client !== null && $client->getId() !== $this->id;
				}),
				"parent_clients" => array_filter(ClientManager::getAll(), function(?Client $client) : bool{
					return $client !== null && $client->getId() !== $this->id && $client->getParent() === $this->parent;
				}),
				default => [ClientManager::getByName($targets)],
			};
		}else{
			$targets = array_map(function(string $target) : ?Client{
				return ClientManager::getByName($target);
			}, $targets);
		}
		foreach($targets as $target){
			$target?->sendPacket(PacketBuilder::create(PacketId::FORWARD, $payload));
		}
	}

	private function handleRequest(array $payload){
		$id = (int) $payload["id"];
		$method = $payload["method"];
		$payload = $payload["data"];
		switch($method){
			case "total_clients":
				$this->sendResponse($id, count(ClientManager::getAll()));
				break;
		}
	}

	public function sendResponse(int $id, mixed $payload){
		$this->sendPacket(ResponseBuilder::create($id, $payload));
	}
}