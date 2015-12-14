<?php

namespace Games\SnakeBundle\Tests\Utils;
use Games\SnakeBundle\Utils\PlayerManager;
use Games\SnakeBundle\Utils\SnakePlayer;
use Games\SnakeBundle\Tests\PHPUnitUtils;

class PlayerManagerTest extends \PHPUnit_Framework_TestCase{
    private $redis;
    private $playerBody;
    private $playerId ;
    private $playerStartLocation;
    
    public function setUp() {
        $this->redis = $this->getMockBuilder("\Predis\Client")
                      ->setMethods(array("hGet","hSet","hGetAll", "hLen", "hExists", "get", "set","del"))
                      ->disableOriginalConstructor()
                      ->getMock();
        
        
        $this->playerId = "12d34e5f6w6vh63==";
        $this->playerBody = "{'body':[{'x':100,'y':100},{'x':125,'y':100},{'x':150,'y':100}],"
                              . "'score':100 ,"
                              . "'className':\"player playerNo1\","
                              . "'inGame':true}";
        $this->playerStartLocation = "[{'body':[{'x':100,'y':100},{'x':125,'y':100},{'x':150,'y':100}],"
                                . "'className':\"player PlayerNo3\"}]";
    }
    public function testMovePlayer() {
        
    }

    public function testSavePlayer() {

        $this->redis->expects($this->once())
                    ->method("hSet")
                    ->with($this->matchesRegularExpression("/^[a-zA-Z.]*$/"),
                            $this->stringContains($this->playerId),
                            $this->isJson());
        $manager = new PlayerManager($this->redis);
        
        $player = new SnakePlayer(json_decode($this->playerBody));
        $reflection = PHPUnitUtils::getSecuredMethod($manager, "savePlayer");
        $reflection->invoke($manager,  $this->playerId, $player);
        
    }
    public function testNewPlayer() {
        
        $this->redis->expects($this->once())
                    ->method("hLen")
                    ->with($this->matchesRegularExpression("/^[a-zA-Z.]*$/"))
                    ->willReturn(5);
        
        $this->redis->expects($this->once())
                    ->method("hExists")
                    ->with($this->matchesRegularExpression("/^[a-zA-Z.]*$/"),
                            $this->stringContains($this->playerId))
                    ->willReturn(false);
        
        $manager = $this->getMockBuilder("\Games\SnakeBundle\Utils\PlayerManager")
                        ->setConstructorArgs(array($this->redis))
                        ->setMethods(array("savePlayer", "getNewSnakeLocation"))
                        ->getMock();
        
        $manager->expects($this->once())
                ->method("getNewSnakeLocation")
                ->willReturn(json_decode("{'body':[{'x':100,'y':100},{'x':125,'y':100},{'x':150,'y':100}],"
                                . "'className':\"player PlayerNo3\"}"));
        
        $manager->expects($this->once())
                ->method("savePlayer")
                ->with($this->stringContains($this->playerId),
                       $this->isInstanceOf("\Games\SnakeBundle\Utils\SnakePlayer"));
        
        $player = $manager->newPlayer($this->playerId);
        
        $this->assertInstanceOf("\Games\SnakeBundle\Utils\SnakePlayer", $player);
        
        return $player;
    }
    public function testGetPlayer() {
        $this->redis->expects($this->once())
                    ->method("hGet")
                    ->with($this->matchesRegularExpression("/^[a-zA-Z.]*$/"),
                            $this->stringContains("12d34e5f6w6vh63=="))
                    ->willReturn($this->playerBody);
        
        $manager = new PlayerManager($this->redis);
        $player = $manager->getPlayer($this->playerId);
        
        $this->assertInstanceOf("\Games\SnakeBundle\Utils\SnakePlayer", $player);
        
    }
    public function testGetPlayers(){
        $players = [$this->playerId=>$this->playerBody];
        
        
        $this->redis->expects($this->once())
              ->method("hGetAll")
              ->willReturn($players);
        
        $manager = new PlayerManager($this->redis);
        $this->assertJson($manager->getPlayers()[$this->playerId]);
    }
    public function testGetLocations() {

        $this->redis->expects($this->once())
                    ->method("get")
                    ->willReturn($this->playerStartLocation);
        
        $manager = new PlayerManager($this->redis);
        $locations = $manager->getLocations();
        
        $this->assertInstanceOf( "\stdClass", $locations[0]);
        
        return $locations;
    }
    /**
     * @depends testGetLocations
     */
    public function testSetLocations($locations) {
        $this->redis->expects($this->once())
                    ->method("set")
                    ->with($this->matchesRegularExpression("/^[a-zA-Z.]*$/"),
                           $this->isJson());
        
        $manager = new PlayerManager($this->redis);
        $manager->setLocations($locations);
    }
    /**
     * @depends testGetLocations
     */
    public function testNewSnakeLocation($locations) {
        $manager = $this->getMockBuilder("\Games\SnakeBundle\Utils\PlayerManager")
                        ->disableOriginalConstructor()
                        ->setMethods(array("getLocations","setLocations"))
                        ->getMock();
        $manager->expects($this->once())
                ->method("getLocations")
                ->willReturn($locations);
        $manager->expects($this->once())
                ->method("setLocations")
                ->with($this->callback(function($ChangedLocation)use($locations){
                    return count($ChangedLocation) < count($locations);
                }));
        $reflection = PHPUnitUtils::getSecuredMethod($manager, "getNewSnakeLocation");
        $location = $reflection->invoke($manager);
    }
    public function testGetBugs() {
        $bugs = "[{'x':100,'y':100},{'x':150,'y':250},{'x':250,'y':375}]";
        $this->redis->expects($this->once())
                    ->method("get")
                    ->willReturn($bugs);
        $manager = new PlayerManager($this->redis);
        $bugsArray = $manager->getBugs();
        
        $this->assertInstanceOf("\stdClass",$bugsArray[0]);
        $this->assertObjectHasAttribute("x", $bugsArray[0]);
        
        return $bugsArray;
    }
    /**
     * @depends testGetBugs
     */
    public function testSaveBugs($bugs) {
        $this->redis->expects($this->once())
                    ->method("set")
                    ->with($this->matchesRegularExpression("/^[a-zA-Z.]*$/"),
                            $this->isJson());
        
        $manager = new PlayerManager($this->redis);
        $manager->saveBugs($bugs);
    }
    public function testResetGame() {
        $this->redis->expects($this->once())
                    ->method("del")
                    ->with($this->matchesRegularExpression("/^[a-zA-Z.]*$/"));
        
        $manager = $this->getMockBuilder("\Games\SnakeBundle\Utils\PlayerManager")
                        ->setConstructorArgs(array($this->redis))
                        ->setMethods(array("setLocations", "saveBugs"))
                        ->getMock();
        $callback = function($args){
            return count($args) > 0;
        };
        
        $manager->expects($this->once())
                ->method("setLocations")
                ->with($this->callback($callback));
        
        $manager->expects($this->once())
                ->method("saveBugs")
                ->with($this->callback($callback));
        
        $manager->resetGame(json_decode($this->playerStartLocation));
    }
}
