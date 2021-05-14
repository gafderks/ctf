<?php
// src/Team.php

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class Team
 *
 * @Entity
 */
class Team
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @var string
     * @Column(type="string")
     */
    protected $teamName;
    
    /**
     * One Product has Many Features.
     * @OneToMany(targetEntity="Capture", mappedBy="team")
     * @var \Capture
     */
    private $captures;
    
    public function getId() {
        return $this->id;
    }
    
    public function getTeamName() {
        return $this->teamName;
    }

    public function getColor() {
        // https://medialab.github.io/iwanthue/
        $COLORS = [
            "#4facd8",
            "#d6602f",
            "#7861d0",
            "#79b63d",
            "#c157b9",
            "#82b76c",
            "#d94174",
            "#4fbe9f",
            "#9d4a6d",
            "#3a8147",
            "#db88b6",
            "#cea147",
            "#757dc6",
            "#7d752f",
            "#c1624f"
        ];
        return $COLORS[$this->getId() % count($COLORS)];
    }
    
    public function __construct($teamName) {
        $this->teamName = $teamName;
        $this->captures = new ArrayCollection();
    }
    
    public function addCapture(\Capture $capture) {
        $this->captures->add($capture);
    }
    
    public function getScore() {
        $num_captures = 0;
        $score = 0;
        foreach($this->captures as $capture) {
            if ($capture->isActive()) {
                $num_captures++;
                $score += $capture->getLocation()->getScore();
            }
        }
        
        return [
            "name" => $this->getTeamName(),
            "color" => $this->getColor(),
            "captures" => $num_captures,
            "total_captures" => count($this->captures),
            "score" => $score
        ];
    }
    
}