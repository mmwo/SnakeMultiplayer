<?php
namespace Games\SnakeBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
/**
 * On production it occured that rpcs disappear after some idle time,
 * command was created for cron job to ping all rpcs before they disappear
 * 
 */

class RPCRespiratorCommand extends ContainerAwareCommand{
    
    protected function configure() {
        $this->setName("rpc:respirator");
    }
    protected function execute(InputInterface $input, OutputInterface $output) {

        $client = $this->getContainer()->get("thruway.client");
        $accesible = true;
        $rpcs = ["games.snake.newplayer",
                 "games.snake.move",
                 "games.snake.startgame",
                 "games.snake.activity"
                ];
        try{
            $output->writeln("tests rpcs availability");
            foreach($rpcs as $rpc){
              $client->call($rpc, [["test"=>"test"]])->then(
                  function ($res) use ($output){
                      $output->writeln( json_encode($res));
                  },  function ($err)use (&$accesible){
                      $accesible = false;
                  }
              );
            }
            if(!$accesible) $output->writeln("Przypal");
       
        } catch (Exception $ex) {
            $output->writeln($ex);
        }

    }

}
