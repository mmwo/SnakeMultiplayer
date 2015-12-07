<?php
namespace Games\SnakeBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RPCRespiratorCommand extends ContainerAwareCommand{
    
    protected function configure() {
        $this->setName("rpc:respirator");
    }
    protected function execute(InputInterface $input, OutputInterface $output) {

        $client = $this->getContainer()->get("thruway.client");

        $data = ["test"=>true];
        try{
            $output->writeln("probuje wyslac na chat cos");
                        
            $client->call("games.snake.newplayer", [["test"=>"test"]])->then(
                function ($res) use ($output){
                    $output->writeln( json_encode($res));
                },  function ($err)use ($output){
                    $output->writeln( $err);
                }
            );
            
            
        } catch (Exception $ex) {
            $output->writeln("kupa blada");
        }

    }

}
