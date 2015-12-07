<?php

namespace Games\SnakeBundle\Utils;

abstract class AbstractPlayer {
    protected $score;
    public function getScore() {
        return $this->score;
    }
    public function incScore($points) {
        $this->score +=$points;
    }
}
