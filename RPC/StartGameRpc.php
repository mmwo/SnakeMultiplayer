<?php

namespace Games\SnakeBundle\RPC;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Voryx\ThruwayBundle\Annotation\Register;

class StartGameRpc extends Controller{
    
    
    /**
     * @Register("games.snake.startgame",serializerEnableMaxDepthChecks=true, worker="start-game-snake")
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
            

            $this->publish(array("gameCreation" => true));

            return array("availability"=> true); 
        }else{
            return array("availability"=>false);
        }
       
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
