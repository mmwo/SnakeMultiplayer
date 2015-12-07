<?php
namespace Games\SnakeBundle\Utils;

class PlayerManager {
    
    const MAX_PLAYERS = 5;
    /**
     * @var type  in memory cache
     */
    protected $redis;
    
    public function __construct($redis) {
        $this->redis = $redis;
    }
    
    public function moveSnakeModules(SnakeBoard $board, $sessionId , $direction) {
        $board->setPlayers($this->getPlayers());
        $board->setBugs($this->getBugs());
        
        $player = $this->getPlayer($sessionId);
        if(!$player){
            return null;
        }
        
        if($player->isInGame() ){
            $board->movePlayer($player, $direction);             
            $this->saveBugs($board->getBugs());
            $this->savePlayer($sessionId, $player);
        }else{
            return false;
        }
    }

    public function savePlayers() {
        
        foreach($players as $id=>$player){   
            $this->savePlayer($id, $player);
        } 
        
    }
    public function getPlayers() {
        return $this->redis->hGetAll("snake.players");
    }
    public function savePlayer($id, $player) {
        $this->redis->hSet("snake.players",$id, 
            json_encode(array(
                "body" => $player->getBody(),
                "score"=> $player->getScore(),
                "className"=> $player->getClass(),
                "inGame"=> $player->isInGame()
            ))); 
    }
    /**
     * @return \Games\SnakeBundle\Utils\SnakePlayer
     */
    public function getPlayer($sessionId) {
        //make obj out of the data from cache
       $playerOptions =  json_decode($this->redis->hget("snake.players", $sessionId));
       return new SnakePlayer($playerOptions);
    }
    public function newPlayer($sessionId) {

        if($this->redis->hLen("snake.players") < $this::MAX_PLAYERS && 
                !$this->redis->hExists("snake.players", $sessionId)){
            
            $location = $this->getNewSnakeLocation();
            if($location){

                $player = new SnakePlayer($location);
                $this->savePlayer($sessionId, $player);

                return $player;
            }
        }
    }
    public function resetGame($snakeInitLocations) {
        $bugs = [
                    ['x'=>50,  'y'=>25,  'className'=>'bug'],
                    ['x'=>25,  'y'=>400, 'className'=>'bug'],
                    ['x'=>175, 'y'=>325, 'className'=>'bug'],
                    ['x'=>275, 'y'=>150, 'className'=>'bug'],
                    ['x'=>375, 'y'=>250, 'className'=>'bug'],
                    ['x'=>500, 'y'=>300, 'className'=>'bug'],
                    ['x'=>575, 'y'=>125, 'className'=>'bug'],
                    ['x'=>700, 'y'=>425, 'className'=>'bug'],
                ];
        $this->setLocations($snakeInitLocations);
        $this->saveBugs($bugs);
        $this->redis->del("snake.players");
        
    }
    public function setLocations($locations) {
        $this->redis->set("snake.locations", json_encode($locations));        
    }
    public function getLocations() {
        return json_decode($this->redis->get("snake.locations"));   
    }
    protected function getNewSnakeLocation() {
        $locations = $this->getLocations();
        shuffle($locations);
        $newLocation =  array_shift($locations);
        $this->setLocations($locations);
        
        return $newLocation;
    }
    public function saveBugs($bugs) {
        $this->redis->set("snake.bugs", json_encode($bugs));
    }
    public function getBugs() {
        return json_decode($this->redis->get("snake.bugs"));
    }
}

