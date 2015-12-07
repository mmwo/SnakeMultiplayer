<?php

namespace Games\SnakeBundle\Controller;

use Games\SnakeBundle\Entity\SnakeStartLocations;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SnakeController extends Controller
{
    public function indexAction()
    {
//        Default snake start locations for db

//        $this->addSnakeLocation(75,50,25,125,125,125,"r", "snakeNo1");
//        $this->addSnakeLocation(75,50,25,225,225,225,"r", "snakeNo2");
//        $this->addSnakeLocation(75,50,25,350,350,350,"r", "snakeNo3");
//        $this->addSnakeLocation(300,325,350,450,450,450,"l", "snakeNo4");
//        $this->addSnakeLocation(400,400,400,350,375,400,"u", "snakeNo5");
//        $this->addSnakeLocation(475,450,425,450,450,450,"r", "snakeNo6");
//        $this->addSnakeLocation(675,700,725,325,325,325,"l", "snakeNo7");
//        $this->addSnakeLocation(675,700,725,200,200,200,"l", "snakeNo8");
//        $this->addSnakeLocation(675,700,725,100,100,100,"l", "snakeNo9");
//        $this->addSnakeLocation(375,375,375,125,100,75,"d", "snakeNo10");
//        $this->addSnakeLocation(475,450,425,25,25,25,"r", "snakeNo11");
//        $this->addSnakeLocation(300,325,350,25,25,25,"l", "snakeNo12");
        
        return $this->render('GamesSnakeBundle:Snake:index.html.twig');
        
    }
    protected function addSnakeLocation($x1,$x2,$x3,$y1,$y2,$y3,$d,$class) {
        $em = $this->getDoctrine()->getManager();
        
        $body = [["x"=>$x1,"y"=>$y1,"d"=>$d],["x"=>$x2,"y"=>$y2,"d"=>$d],["x"=>$x3,"y"=>$y3,"d"=>$d]];
        
        $snake = new SnakeStartLocations();
        $snake->setBody(json_encode($body));
        $snake->setClassName("player ".$class);
        
        $em->persist($snake);
        $em->flush();
    }
}
