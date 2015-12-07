<?php

namespace Games\SnakeBundle\RPC;
use Voryx\ThruwayBundle\Annotation\Register;
use Thruway\Authentication\AuthenticationManager;
use Voryx\ThruwayBundle\Annotation\Worker;

/**
 * @Worker("pingpong-snake")
 */
class PingPong extends RpcController{
    /**
     * @Register("games.snake.activity",serializerEnableMaxDepthChecks=true, )
     */
    public function pingpong($requestData){

        $requestData = (object)$requestData;
        if(property_exists($requestData, "test")){
            return array("response"=>true);
        }
        
        $salt = $this->getParameter("salt.of.awesomeness");
        
        $this->publish("games.snake.game", array("id"=> base64_encode(crypt($requestData->sessionId,$salt)),
                             "data" => array("name" => $requestData->name,
                                             "time"=> $requestData->time
                        )));
    }

    
}
