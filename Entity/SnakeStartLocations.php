<?php

namespace Games\SnakeBundle\Entity;

/**
 * SnakeStartLocations
 */
class SnakeStartLocations
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $body;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set body
     *
     * @param string $body
     *
     * @return SnakeStartLocations
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Get body
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }
    /**
     * @var string
     */
    private $className;


    /**
     * Set className
     *
     * @param string $className
     *
     * @return SnakeStartLocations
     */
    public function setClassName($className)
    {
        $this->className = $className;

        return $this;
    }

    /**
     * Get className
     *
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }
}
