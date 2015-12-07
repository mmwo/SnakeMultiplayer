<?php

namespace Games\SnakeBundle\RPC;
use Voryx\ThruwayBundle\Annotation\Register;
use Voryx\ThruwayBundle\Annotation\Worker;

/**
 * @Worker("start-game-snake")
 */
class StartGameRpc extends RpcController{
    
    
    /**
     * @Register("games.snake.startgame",serializerEnableMaxDepthChecks=true)
     */
    public function startGame($requestData){
        $requestData = (object)$requestData;
        if(property_exists($requestData, "test")){
            return array("response"=>true);
        }
        
        /* @var $pM /Games/SnakeBundle/Utils/PlayerManager   */
        $pM = $this->get("snake.player_manager");
       
        $repo = $this->getDoctrine()->getRepository("GamesSnakeBundle:SnakeStartLocations");
        $snakeInitLocations = $repo->findAllFetchArray();
        if($snakeInitLocations){
            $pM->resetGame($snakeInitLocations);
            

            $this->publish("games.snake.game", array("gameCreation" => true));

            return array("availability"=> true); 
        }else{
            return array("availability"=>false);
        }
       
    }
     
}
