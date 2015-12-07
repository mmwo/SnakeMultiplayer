<?php

namespace Games\SnakeBundle\Utils;

class SnakePlayer extends AbstractPlayer {
    
    
    protected $inGame = true;
    protected $extra = false;
    protected $score = 0;
    protected $body = [];
    protected $className;

    public function __construct($options) {
        
        if(is_string($options->body)){
            $this->body = json_decode($options->body);
        }else{
            $this->body = $options->body;
        }

        $this->className = $options->className;
        if(property_exists($options, "inGame")){
            $this->inGame = $options->inGame;
        }
        if(property_exists($options, "score")){
            $this->score = $options->score;
        }           
    }
    public function getBody() {
        return $this->body;
    }
    public function getClass() {
        return $this->className;
    }
    public function inGame($inGame) {
        if(!$inGame){
            $this->body = [];
        }
        $this->inGame = $inGame;
    }
    public function isInGame(){
        return $this->inGame;
    }
    public function getScore() {
        return $this->score;
    }
    public function addExtra() {
        $this->extra = true;
    }
    public function incScore($points) {
        $this->score +=$points;
    }
    
    public function setModule($module){
        array_unshift($this->body, $module);
        
        if($this->extra){ //module from end is not removed when true
            $this->extra = false;
        }else{
            array_pop($this->body);
        }     
    }
    public function getHead() {
        return $this->body[0];
    }
    




    
}
