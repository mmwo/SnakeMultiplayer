<?php
namespace Games\SnakeBundle\Tests\Utils;

use Games\SnakeBundle\Utils\SnakePlayer;


class SnakePlayerTest extends \PHPUnit_Framework_TestCase{
    protected $options = ["body"=>[["x"=>25, "y"=>100,"d"=>"r"],
                                   ["x"=>50, "y"=>100,"d"=>"r"], 
                                   ["x"=>75, "y"=>100,"d"=>"r"]
                                  ],
                          "className"=>"player playerNo3"
            ];

    public function testBodySet() {
        //body is object
        $options = (object)$this->options;
        $snake = new SnakePlayer($options);

        $this->assertEquals($options->body, $snake->getBody());
        $this->assertGreaterThan(0, count($snake->getBody()));
    }
    public function testBodySet2() {
        // body is json string
        $options = (object)$this->options;
        $options->body = json_encode($options->body);
        
        $snake = new SnakePlayer($options);
        
        $this->assertEquals(json_decode($options->body), $snake->getBody());
        $this->assertGreaterThan(0, count($snake->getBody()));
    }
    public function testPlayerGameOver() {
        $options = (object)  $this->options;
        $snake = new SnakePlayer($options);
        
        $snake->inGame(false);
        $this->assertEquals(0, count($snake->getBody()));
    }
    public function testIsInGame() {
        $options = (object)$this->options;
        $options->inGame = true;
        
        $snake = new SnakePlayer($options);
        
        $this->assertEquals(true, $snake->isInGame());
        
        $snake->inGame(false);
        
        $this->assertEquals(false, $snake->isInGame());
    }
    public function testScoreIncrease() {
        $options = (object)$this->options;
        $options->score = 100;
        
        $snake = new SnakePlayer($options);
        
        $this->assertSame(100, $snake->getScore());
        
        $snake->incScore(25);
        $this->assertSame(125, $snake->getScore());
    }
    public function testSetModule() {
        $options = (object)$this->options;
        $snake = new SnakePlayer($options);
        $module = ["x"=>125,"y"=>100,"d"=>"r"];
        // adding module by default no points gained
        $snake->setModule($module);
        $this->assertSame(3, count($snake->getBody()));
        // adding module when points gained
        $snake->addExtra();
        $snake->setModule($module);
        $this->assertSame(4, count($snake->getBody()));
    }
    public function testGetClass() {
        $options = (object)$this->options;
        $snake = new SnakePlayer($options);
        
        $this->assertEquals($options->className, $snake->getClass());
    }
    public function testGetHead() {
        $options = (object)$this->options;
        $snake = new SnakePlayer($options);
        
        $this->assertEquals($options->body[0], $snake->getHead());
    }
}
