<?php
$config = include('config.php');
require_once('bootstrap.php');

function createTeam($name) {
    global $entityManager;
    global $config;
    if (is_null(getTeamByName($name))) {
        try {
            
            $team = new \Team($name);
            $entityManager->persist($team);
            $entityManager->flush();
        } catch (Exception $e) {
   var_dump($e->getTrace());}

    
    }
}

function captureLocation($name, $locationId) {
    global $entityManager;
    global $config;
    $team = getTeamByName($name);
    $location = getLocation($locationId);
    $capture = new \Capture($location, new \DateTime('now'), $team);
    $team->addCapture($capture);
    $entityManager->persist($team);
    $entityManager->persist($capture);
    $entityManager->flush();
}

/**
 * @return \Team[]
 */
function getTeams() {
    global $entityManager;
    return $entityManager->getRepository('Team')->findAll();
}

/**
 * @return \Team[]
 */
function getLocations() {
    global $entityManager;
    return $entityManager->getRepository('Location')->findBy(['enabled' => true]);
}

/**
 * @param $id
 *
 * @return \Location
 */
function getLocation($id) {
    global $entityManager;
    return $entityManager->getRepository('Location')->find($id);
}

/**
 * @param $id
 * @return \Team
 */
function getTeamByName($name) {
    global $entityManager;
    return $entityManager->getRepository('Team')->findOneBy(['teamName' =>
                                                                 $name]);
}

function getNotifications() {
    global $entityManager;
    return $entityManager->getRepository('Notification')->findAll();
}