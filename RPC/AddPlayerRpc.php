<?php

namespace Games\SnakeBundle\RPC;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Voryx\ThruwayBundle\Annotation\Register;
use Games\SnakeBundle\Utils\SnakePlayer;

class AddPlayerRpc extends Controller{
    
    /**
     * @Register("games.snake.newplayer",serializerEnableMaxDepthChecks=true, worker="add-snake")
     */
    public function addPlayer($requestData){
        $requestData = (object)$requestData;
        if(property_exists($requestData, "test")){
            $this->get("logger")->info("test message received");
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
        

        $this->publish(array("id" => $id,
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
    
    protected function publish(array $data) {
        $client = $this->get("thruway.client");
        $client->publish("games.snake.game", [$data],[],
        ["acknowledge" => true])->then(
            function(){},
            function($error){
                $this->get('logger')->info(json_encode($error));
        });
    }
     
}
