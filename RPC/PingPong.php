<?php

namespace Games\SnakeBundle\RPC;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Voryx\ThruwayBundle\Annotation\Register;
use Thruway\Authentication\AuthenticationManager;

class PingPong extends Controller{
    /**
     * @Register("games.snake.activity",serializerEnableMaxDepthChecks=true, worker="pingpong-snake")
     */
    public function pingpong($requestData){

        $requestData = (object)$requestData;
        if(property_exists($requestData, "test")){
            return array("response"=>true);
        }
        
        $salt = $this->getParameter("salt.of.awesomeness");
        
        $this->publish(array("id"=> base64_encode(crypt($requestData->sessionId,$salt)),
                             "data" => array("name" => $requestData->name,
                                             "time"=> $requestData->time
                        )));
    }
    protected function publish(array $data) {
        $client = $this->get("thruway.client");
        
        $client->publish("games.snake.game", [$data],[],["acknowledge" => true])->then(
        function($response){},
        function($error){
            $this->get('logger')->info(json_encode($error));
        });
    }
    
}
