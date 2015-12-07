<?php

namespace Games\SnakeBundle\Utils;

class SnakeBoardEngine {

    public function computeMovement(SnakeBoard $board, SnakePlayer $player, $module) {
        
        if($this->isOccupied($board, $module->x,$module->y) 
           || $this->isBorder($board, $module->x,$module->y)){
            $player->inGame(false);
            
            return false;  
        }
        
        if($this->isBug($board, $module->x, $module->y)){
            $player->addExtra();
            $player->incScore(25);
            $board->addBug();
        } 
        
        $player->setModule($module);
        return true;
    }
    public function isOccupied(SnakeBoard $board, $x, $y) {
        foreach($board->getPlayers() as $options){
            $playerOptions = json_decode($options);
            foreach($playerOptions->body as $playerModule){
                if($playerModule->x === $x && $playerModule->y === $y){
                    return true;
                }
            }
        }
    }
    public function isBorder(SnakeBoard $board, $x, $y){
        if(($x >= 0 && $x <= $board::boardWidth)
          && ($y >= 0 && $y <= $board::boardHeight))
        {
            return false;
        }else{
            return true;
        }
    }
    public function isBug(SnakeBoard $board, $x, $y) {
        $bugs = $board->getBugs();
        for ($i = 0; $i < count($bugs);$i++){
            if($bugs[$i]->x == $x && $bugs[$i]->y == $y){
                array_splice($bugs, $i, 1);
                $board->setBugs($bugs);
                return true;
            }
        }
        return false;
    }
    
   
}
