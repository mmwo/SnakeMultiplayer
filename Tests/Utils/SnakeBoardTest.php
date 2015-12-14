<?php

namespace Games\SnakeBundle\Tests\Utils;

use Games\SnakeBundle\Utils\SnakeBoard;
use Games\SnakeBundle\Utils\SnakeBoardEngine;
use Games\SnakeBundle\Tests\PHPUnitUtils;

class SnakeBoardTest extends \PHPUnit_Framework_TestCase{
    public function testAddBug() {
        $engine = $this->getMockBuilder("\Games\SnakeBundle\Utils\SnakeBoardEngine")
                       ->setMethods(array("isOccupied","isBug"))
                       ->getMock();
        
        $engine->expects($this->any())
               ->method("isOccupied")
               ->willReturn(false);
        
        $engine->expects($this->any())
               ->method("isBug")
               ->willReturn(false);
        
        $board = new SnakeBoard($engine);
        $board->setBugs([]);
        
        $board->addBug();
        
        $this->assertSame(1, count($board->getBugs()));
    }
    public function testMovePlayer() {
        $direction = "r";
        $module = new \stdClass();
        
        $engine = $this->getMockBuilder("\Games\SnakeBundle\Utils\SnakeBoardEngine")
                       ->setMethods(array("computeMovement"))
                       ->getMock();
        
        $engine->expects($this->once())
               ->method("computeMovement")
               ->with($this->isInstanceOf("\Games\SnakeBundle\Utils\SnakeBoard"),
                      $this->isInstanceOf("\Games\SnakeBundle\Utils\SnakePlayer"),
                      $this->isInstanceOf( new \stdClass()));
        
        $player = $this->getMockBuilder("\Games\SnakeBundle\Utils\SnakePlayer")
                       ->disableOriginalConstructor()
                       ->getMock();
        
        $board = $this->getMockBuilder("\Games\SnakeBundle\Utils\SnakeBoard" )
                      ->setMethods(array("createMotionModule"))
                      ->setConstructorArgs(array($engine))
                      ->getMock();
        
        $board->expects($this->once())
              ->method("createMotionModule")
              ->with($this->isInstanceOf("\Games\SnakeBundle\Utils\SnakePlayer"),
                     $this->matchesRegularExpression('/^r|l|d|u/'))
              ->willReturn($module);
        
        $board->movePlayer($player, $direction);
    }
    /**
     * @dataProvider directionChange
     */
    public function test_calcDirection($directionRequested, $headDirection, $direction) {
        $board = new SnakeBoard(new SnakeBoardEngine());
        $reflection = PHPUnitUtils::getSecuredMethod($board, "calcDirection");
        
        
        $this->assertEquals($direction,
                    $reflection->invoke($board, $directionRequested,$headDirection ));
        
    }
    public function test_createMotionModule() {
        $head = (object)["x"=>100,"y"=>100,"d"=>"r"];
        $player = $this->getMockBuilder("\Games\SnakeBundle\Utils\SnakePlayer")
                       ->disableOriginalConstructor()
                       ->setMethods(array("getHead"))
                       ->getMock();
        $player->expects($this->exactly(2))
               ->method("getHead")
               ->willReturn($head);
        $board = new SnakeBoard(new SnakeBoardEngine());
        
        $reflection = PHPUnitUtils::getSecuredMethod($board, "createMotionModule");
        
        $this->assertObjectHasAttribute("x", $reflection->invoke($board, $player, "u"));
        $this->assertObjectHasAttribute("y", $reflection->invoke($board, $player, "d"));
            

        
    }
    
    public function directionChange() {
        return array(
                     ["r","l","l"],
                     ["l","r","r"],
                     ["u","d","d"],
                     ["d","u","u"],
                     ["l","u","l"],
                     ["r","u","r"],
                     ["r","d","r"],
                     ["r","u","r"]
                    );
    }
}
