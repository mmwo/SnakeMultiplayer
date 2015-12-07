<?php

namespace Games\SnakeBundle\Utils;

class SnakeBoard  {
    const boardWidth = 775;
    const boardWidthPoints = 31;
    const boardHeight = 500;
    const boardHeightPoints = 20;
    const distance = 25;
    protected $forbiddenDirection = [ ["l","r"],["r","l"],["u","d"],["d","u"] ];
    /**
     * @var SnakeBoardEngine engine of the board
     */
    protected $engine;
    
    protected $players;
    protected $bugs;
    public $logger;
    public function __construct(SnakeBoardEngine $engine) {
        $this->engine = $engine;

    }
    public function addBug(){
        do{
            $x = rand(0,self::boardWidthPoints) * self::distance;
            $y = rand(0,self::boardHeightPoints) * self::distance;

            if(!$this->engine->isOccupied($this, $x, $y) && 
               !$this->engine->isBug($this, $x, $y))
            {
                $bug = array("x" =>$x, "y"=>$y, "className"=>"bug");

                array_push($this->bugs, $bug);
            }else{
                $bug = false;
            }
        }while($bug === false);
    }
    public function movePlayer(SnakePlayer $player, $direction) {
        $module = $this->createMotionModule($player, $direction);
        return $this->engine->computeMovement($this, $player, $module);
    }
    public function getPlayers() {
        return $this->players;
    }
    public function setPlayers($players) {
        $this->players = $players;
    }
    public function getBugs() {
        return $this->bugs;
    }
    public function setBugs($bugs) {
        $this->bugs = $bugs;
    }
    protected function createMotionModule(SnakePlayer $player, $direction) {
        $head = $player->getHead();
        $module = new \stdClass();
        $direction = $this->calcDirection($direction, $player);

        switch ($direction){
            case "l" : $module->x = $head->x + minus(self::distance);
                       $module->y  = $head->y; break;
            case "r" : $module->x = $head->x + self::distance;
                       $module->y  = $head->y; break;
            case "u" : $module->y  = $head->y + minus(self::distance);
                       $module->x = $head->x; break;
            case "d" : $module->y  = $head->y + self::distance;
                       $module->x = $head->x; break;                    
        }
        
        $module->d = $direction;

        return $module;
    }
    protected function calcDirection($direction, $player){
        $action = array($direction,$player->getHead()->d );
        
        // if(in_array($action,$this->forbiddenDirection){
        //     $direction = $action[1];
        // }
        foreach($this->forbiddenDirection as $value){
            if($action[0] == $value[0] && $action[1] == $value[1]){
                return $value[1];
            }
        }

        return $direction;
    }
}

function minus($value){
    return -$value;
}