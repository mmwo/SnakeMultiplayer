<?php

namespace Games\SnakeBundle\RPC;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class RpcController extends Controller{
    protected function publish($uri, array $data) {
        $client = $this->get("thruway.client");
        $client->publish($uri, [$data],[],
        ["acknowledge" => true])->then(
            function(){},
            function($error){
                $this->get('logger')->info(json_encode($error));
        });
    }    
}
