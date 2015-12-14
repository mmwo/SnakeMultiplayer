<?php
/**
 *  test covers part of code when movement is available
 */
namespace Games\SnakeBundle\Tests\Utils;

use Games\SnakeBundle\Utils\SnakeBoardEngine;
use Games\SnakeBundle\Utils\SnakeBoard;

class SnakeBoardEngineTest extends \PHPUnit_Framework_TestCase{
    

    public function testComputeMovement( ) {
        
        $engine = $this->getMockBuilder("\Games\SnakeBundle\Utils\SnakeBoardEngine")
                       ->setMethods(array("isBorder","isOccupied","isBug"))
                       ->getMock();
        
        $engine->expects($this->any())
               ->method("isBorder")
               ->willReturn(false);
        
        $engine->expects($this->any())
               ->method("isOccupied")
               ->willReturn(false);
        // nothing happens when bug is false lets test when is true then
        $engine->expects($this->once())
               ->method("isBug")
               ->willReturn(true);
        
        $board = $this->getMockBuilder("\Games\SnakeBundle\Utils\SnakeBoard")
                      ->disableOriginalConstructor()
                      ->setMethods(array("addBug"))
                      ->getMock();
        // add bug must be called when bug exists
        $board->expects($this->once())
              ->method("addBug");
        
        // player gets extra module and score when got bug
        $player = $this->getMockBuilder("\Games\SnakeBundle\Utils\SnakePlayer")
                       ->disableOriginalConstructor()
                       ->setMethods(array("addExtra","incScore"))
                       ->getMock();
        $player->expects($this->once())
               ->method("addExtra");
        $player->expects($this->once())
               ->method("incScore")
               ->with($this->greaterThan(0));       
        
        $module = new \stdClass();
        $module->x  = 100;
        $module->y  = 100;
        
        $this->assertTrue($engine->computeMovement($board, $player, $module),
                "Movement is not Computable when it shoul");

    }    
    /**
     * @dataProvider snakeModulePosition
     */
    public function testBugIsOnField($x, $y, $result) {
        $bugs = array((object)["x"=>100, "y"=>100], (object)["x"=>100,"y"=>200]);
        
        $board = $this->getMockBuilder("\Games\SnakeBundle\Utils\SnakeBoard")
                      ->disableOriginalConstructor()
                      ->setMethods(array("getBugs","setBugs"))
                      ->getMock();
        
        $board->expects($this->once())
              ->method("getBugs")
              ->willReturn($bugs);
        
        $board->expects($this->any())
              ->method("setBugs")
              ->with($this->callback(function($resultBugs) use ($bugs){
                    return count($bugs) > count($resultBugs );
                }));
        
        $engine = new SnakeBoardEngine();
        
        $this->assertEquals($result, $engine->isBug($board, $x, $y));
    }
    public function snakeModulePosition() {
        return array(
                    [100, 100, true],
                    [200, 150, false],
                    [300, 175, false],
                    [400, 200, false],
                    [-100,100, false],
                    [-100,-100,false],
                    [100,-100, false],
                    [2000,100, false],
                    [100,2000, false],
                    [100, 200, true]
            );
    }
}
