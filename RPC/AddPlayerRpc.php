<?php

namespace Games\SnakeBundle\RPC;
use Voryx\ThruwayBundle\Annotation\Register;
use Voryx\ThruwayBundle\Annotation\Worker;
use Games\SnakeBundle\Utils\SnakePlayer;
/**
 * @Worker("add-snake")
 */
class AddPlayerRpc extends RpcController{
    
    /**
     * @Register("games.snake.newplayer",serializerEnableMaxDepthChecks=true)
     */
    public function addPlayer($requestData){
        $requestData = (object)$requestData;
        if(property_exists($requestData, "test")){
            return array("response"=>true);
        }
        
        /* @var $pM /Games/SnakeBundle/Utils/PlayerManager   */        
        $pM = $this->get("snake.player_manager");

        $salt = $this->getParameter("salt.of.awesomeness");
        $id = base64_encode(crypt($requestData->sessionId, $salt));
        
        $player = $pM->newPlayer($id);
        

        if(!$player){
            return array("availability"=> false);
        }
        

        $this->publish("games.snake.game", array("id" => $id,
                            "data" => array(
                                            "body" => $player->getBody(),
                                            "ingame" => $player->isInGame(),
                                            "score" =>  $player->getScore(),
                                            "className" => $player->getClass(),
                                )));
        
        return array("availability"=> true,
                     "ingame" => $player->isInGame(),
                     "direction"=>$player->getBody()[0]->d,
                     "className" => $player->getClass(),
                    );
    }
     
}
