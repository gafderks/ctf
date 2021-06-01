<?php

include_once "controller.php";
global $entityManager;

header('Content-Type: application/json');

switch ($_GET['action']) {
    case "create":
        switch ($_GET['resource']) {
            case "team":
                createTeam($_POST['name']);
                break;
            case "capture":
                captureLocation($_POST['name'], $_POST['location']);
                break;
            default:
                print "Unsupported resource";
        }
        break;
    case "list":
        switch ($_GET['resource']) {
            case "locations":
                $locations = getLocations();
                $result = [];
                foreach ($locations as $location) {
                    array_push($result, $location->toJSON());
                }
                print json_encode($result);
                break;
            case "scores":
                $teams = getTeams();
                $result = [];
                foreach ($teams as $team) {
                    array_push($result, $team->getScore());
                }
                usort($result, function($a, $b) {
                    return $a['score'] < $b['score'];
                });
                print json_encode($result);
                break;
            case "notifications":
                $notifications = getNotifications();
                $result = [];
                foreach ($notifications as $notification) {
                    array_push($result, $notification->toJSON());
                }
                print json_encode($result);
                break;
            default:
                print "Unsupported resource";
        }
        break;
    default:
        print "Unsupported action";
}