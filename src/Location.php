<?php
// src/Location.php

use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class Location
 *
 * @Entity
 */
class Location
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
    protected $name;
    
    /**
     * @OneToMany(targetEntity="Capture", mappedBy="location")
     * @OrderBy({"timestamp" = "ASC"})
     * @var \Capture[]
     */
    private $captures;
    
    /**
     * @var double
     * @Column(type="decimal")
     */
    private $score;

    /**
     * @var double
     * @Column(type="string")
     */
    private $lat;
    
    /**
     * @var double
     * @Column(type="string")
     */
    private $lon;

    /**
     * @var string
     * @Column(type="string", nullable=true)
     */
    private $polygon_json;

    /**
     * @var boolean
     * @Column(type="boolean")
     */
    private $enabled = true;
    
    public function getId() {
        return $this->id;
    }
    
    public function getCaptures() {
        return $this->captures;
    }
    
    public function getName() {
        return utf8_encode($this->name);
    }

    public function getLat() {
        return utf8_encode($this->lat);
    }
    
    public function getLon() {
        return utf8_encode($this->lon);
    }

    public function getPolygon() {
        return $this->polygon_json;
    }

    public function getScore() {
        return $this->score;
    }

    public function getColor() {
        $activeCapture = $this->getActiveCapture();
        if ($activeCapture != null) {
            return $activeCapture->getTeam()->getColor();
        }
        return 'white';
    }

    public function getActiveCapture() {
        if (!$this->captures->isEmpty()) {
            return $this->captures->last();
        }
        return null;
    }
    
    public function getActiveCaptureTeamName() {
        $activeCapture = $this->getActiveCapture();
        if ($activeCapture != null) {
            return $activeCapture->getTeam()->getTeamName();
        }
        return null;
    }
    
    public function toJSON() {
        $the_captures_json = [];
        foreach ($this->captures as $capture) {
            array_push($the_captures_json, $capture->toJSON());
        }
       
        return [
            "id" => $this->id,
            "name" => $this->getName(),
            "score" => $this->getScore(),
            "location" => [
                "lat" => (double) $this->getLat(),
                "lon" => (double) $this->getLon()
            ],
            "polygon" => $this->getPolygon(),
            "captures" => $the_captures_json,
            "owner" => $this->getActiveCaptureTeamName(),
            "color" => $this->getColor()
        ];
    }
    
    public function __construct() {
        $this->captures = new ArrayCollection();
    }
    
    public function addCapture(\Capture $capture) {
        $this->captures->add($capture);
    }
    
}
