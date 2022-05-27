# Zeber

### Requirements
https://github.com/AkmalFairuz/Sobana

### Usage
- Sending Packet
```php
// Send packet by client name
ClientManager::getByName("AS1-Practice")?->sendPacket(
    ForwardBuilder::create(
        "zeber", 
        [
            "action" => "broadcast_message", 
            "message" => "Hi"
        ]
    )
);
```
- Handling Packet
```php
    // Client.php
    // ...
    public function handlePacket(string $id, mixed $data){
        switch($id) {
            case PacketId::FORWARD:
                $this->handleForward($data);
                break;
            case PacketId::REQUEST:
                $this->handleRequest($data);
                break;
        }
    }
    // ...
    
    // Handle forward packet
    // ...
    private function handleForward(array $payload) {
        $target = $payload["target"];

        $targetClient = ClientManager::getByName($target);
        $targetClient?->sendPacket(
            PacketBuilder::create(
                PacketId::FORWARD,
                $payload
            )
        );
    }
    // ...
```