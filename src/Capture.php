<?php
// src/Capture.php

/**
 * Class Capture
 *
 * @Entity
 */
class Capture
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * Many Transactions have One Team.
     * @ManyToOne(targetEntity="Team", inversedBy="transactions")
     */
    protected $team;
    
    /**
     * @Column(type="datetime")
     */
    protected $timestamp;
    
    /**
     * Many Captures have One Location.
     * @ManyToOne(targetEntity="Location", inversedBy="captures")
     */
    protected $location;
    
    public function getId() {
        return $this->id;
    }
    
    public function getTimestamp() {
        return $this->timestamp;
    }
    
    public function getTeam() {
        return $this->team;
    }

    public function getLocation() {
        return $this->location;
    }

    public function isActive() {
        return $this->getLocation()->getActiveCapture()->getId() == $this->getId();
    }
    
    public function toJSON() {
        return [
            "timestamp" => $this->getTimestamp()->getTimestamp(),
            "team" => $this->getTeam()->getTeamName(),
            "color" => $this->getTeam()->getColor(),
        ];
    }
    
    public function __construct(\Location $location, \DateTime $time, \Team $team) {
        $this->location = $location;
        $this->timestamp = $time;
        $this->team = $team;
    }
}