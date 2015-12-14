<?php

namespace Games\SnakeBundle\Tests\Utils;

use Games\SnakeBundle\Utils\SnakeBoardEngine;
use Games\SnakeBundle\Utils\SnakeBoard;

class SnakeBoardEngineGameOverTest extends \PHPUnit_Framework_TestCase{
    /**
     * @dataProvider movementNotComputable
     */
    public function testComputeMovement( $occupied, $border) {
        // is border and isOccupied is already tested
        $engine = $this->getMockBuilder("\Games\SnakeBundle\Utils\SnakeBoardEngine")
                       ->setMethods(array("isBorder","isOccupied"))
                       ->getMock();
        $engine->expects($this->any())
               ->method("isBorder")
               ->willReturn($border);
        $engine->expects($this->any())
               ->method("isOccupied")
               ->willReturn($occupied);
        
        $board = $this->getMockBuilder("\Games\SnakeBundle\Utils\SnakeBoard")
                      ->disableOriginalConstructor()
                      ->getMock();
        
        
        $player = $this->getMockBuilder("\Games\SnakeBundle\Utils\SnakePlayer")
                       ->disableOriginalConstructor()
                       ->setMethods(array("inGame"))
                       ->getMock();
        $player->expects($this->once())
               ->method("inGame")
               ->with($this->equalTo(false));
        
        $module = new \stdClass();
        $module->x  = 100;
        $module->y  = 100;
        
        $this->assertFalse($engine->computeMovement($board, $player, $module),
                "Movement is Computable when it shoul not");

    }


    public function testModuleIsOnBorder() {
        
                $board = $this->getMockBuilder("\Games\SnakeBundle\Utils\SnakeBoard")
                              ->disableOriginalConstructor()
                              ->getMock();
                // decided not to use dataProvider so that got access to constants
                // of the board required to check width and height;
                $borderPositions = array([-25,0],
                        [-25,100],
                        [-25, $board::boardHeight],
                        [-25, $board::boardHeight+25],
                        [0,-25],
                        [100,-25],
                        [$board::boardWidth,-25],
                        [$board::boardWidth+25, $board::boardHeight],
                        [$board::boardWidth+25, $board::boardHeight+25],
                        [-25,-25],
                        
               );
                
                $engine =  new SnakeBoardEngine();
                
                foreach($borderPositions as $position)
                    $this->assertTrue($engine->isBorder($board, $position[0], $position[1]),
                            "Module is not beyound the border when it should");
    }
    /**
     * @dataProvider snakeHeadModulePosition
     */
    public function testFieldIsOccupied($x, $y) {
        $players = [123456663=>"{'body':[{'x':100,'y':100},{'x':125,'y':100},{'x':150,'y':100}]}",
                    324234234=>"{'body':[{'x':200,'y':100},{'x':200,'y':125},{'x':200,'y':150}]}"];
        
        $board = $this->getMockBuilder("\Games\SnakeBundle\Utils\SnakeBoard")
                      ->disableOriginalConstructor()
                      ->setMethods(array('getPlayers'))
                      ->getMock();
        $board->expects($this->once())
              ->method('getPlayers')
              ->willReturn($players);
        
        $engine = new SnakeBoardEngine();
        $this->assertTrue($engine->isOccupied($board, $x, $y),
                "Field should be occupied by other snake");
    }
    public function movementNotComputable(){
        return array(
            [true, false],
            [false, true],
            [true, true]
        );
    }

    public function snakeHeadModulePosition() {
        return array(
                    [100, 100],
                    [125, 100],
                    [150, 100],
                    [200, 100],
                    [200, 125],
                    [200, 150],
            );
    }
}
