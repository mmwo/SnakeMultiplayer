<?php

namespace Games\SnakeBundle\Utils;

abstract class AbstractPlayer {
    protected $score;
    public function setScore($score) {
        $this->score = $score;
    }
}
