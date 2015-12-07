<?php

namespace Games\SnakeBundle\RPC;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Voryx\ThruwayBundle\Annotation\Register;

class MoveRpc extends Controller{
    /**
     * @Register("games.snake.move",serializerEnableMaxDepthChecks=true, worker="move-snake")
     */
    public function makeAction($requestData){
        $requestData = (object)$requestData;
        
        if(property_exists($requestData, "test")){
            return array("response"=>true);
        }
        
        /* var $pM /Games/SnakeBundle/Utils/PlayerManager  */
        $pM = $this->get("snake.player_manager");
        $salt = $this->getParameter("salt.of.awesomeness");
        $id = base64_encode(crypt($requestData->sessionId,$salt));
        
        $board = $this->get("snake.board");
        $pM->moveSnakeModules($board, $id, $requestData->direction);
        
        $player = $pM->getPlayer($id);
        if(!$player){
            return array("error"=>"Sorry. Session doesn't exist. Please try to refresh page.");
        }
        $params = array(  
                    "id"=> $id,
                    "bugs" => $pM->getBugs(),
                    "data" => array(
                                "body"=> $player->getBody(),
                                "ingame" =>  $player->isInGame(),
                                "score" => $player->getScore(),
                                "className" => $player->getClass(),
                            ));
        $this->publish($params);
        
        return array("ingame"=>$player->isInGame());
    }
    protected function publish(array $data) {
        $client = $this->get("thruway.client");

        $client->publish("games.snake.game", [$data],[],["acknowledge" => true])->then(
        function($response){
        //  success function 
        },
        function($error){
            $this->get->logger->info(json_encode($error));  // log errors
        });
    }
}
