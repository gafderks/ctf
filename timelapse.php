<?php
$config = include('config.php');
include_once('controller.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
  <link href="//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css" rel="stylesheet">
  <link rel="stylesheet" href="css/main.css" />

  <title>Vlaggenroof | Timelapse</title>
  <style>
    @media print {
      .collapse {
        display: block !important;
        height: auto !important;
      }
      
      .hidden-print {
        display: none !important;
      }      
    }

    body {
        background: #CCC;
    }

    #clock {
        width: 200px;
        height: 200px;
        background: #FFF;
        border-radius: 50%;
        position: relative;
    }

    #clock.transform div {
        -moz-transition: all 0.95s linear;
        -webkit-transition: all 0.95s linear;
        -o-transition: all 0.95s linear;
        transition: all 0.95s linear;
    }

    #dot {
        width: 8px;
        height: 8px;
        background: red;
        border-radius: 50%;
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        margin: auto;
        z-index: 12;
    }

    #sec {
        width: 2px;
        height: 80px;
        background: red;
        position: absolute;
        right: 0;
        left: 0;
        top: 20px;
        margin: auto;
        -webkit-transform-origin: center bottom;
        -webkit-transform: rotate(0deg);
        z-index: 10;
    }

    #min {
        width: 4px;
        height: 75px;
        background: black;
        position: absolute;
        right: 0;
        left: 0;
        top: 25px;
        margin: auto;
        -webkit-transform-origin: center bottom;
        -webkit-transform: rotate(0deg);
        z-index: 8;
    }

    #hour {
        width: 4px;
        height: 50px;
        background: black;
        position: absolute;
        right: 0;
        left: 0;
        top: 50px;
        margin: auto;
        -webkit-transform-origin: center bottom;
        -webkit-transform: rotate(0deg);
        z-index: 6;
    }
  </style>
</head>
<body>

<div id="page-map" class="page" style="height: 100%">
  <div class="d-none d-lg-block" style="position: absolute; z-index: 6000; top: 10px; right: 10px; opacity: 0.8;">
    <div id="clock" class="shadow">
        <div id="dot"></div>
        <div id="hour"></div>
        <div id="min"></div>
        <div id="sec"></div>
    </div>
    <div id="currentTime" style="color: gray; width: 100%; text-align: center; margin-top: -170px; position: relative;"></div>
  </div>

  <div class="d-flex" style="position: absolute; z-index: 6000; bottom: 10px; width: calc(100% - 130px); opacity: 0.8; margin: 50px; margin-right: 80px; margin-bottom: 30px;">
  <button type="button" class="btn btn-primary mr-3 shadow" id="togglePlay"><i class="icon-play"></i></button>
    <input style="flex-grow: 1;" type="range" id="timelapse_range" min="0" step="10" value="0">
  </div>

  <div id="map"></div>

  <table class="table table-sm shadow d-none d-lg-block" style="position: absolute; z-index: 6000; top: 80px; left: 10px;
  background: rgba(255, 255, 255, 0.8); width: 300px;">
    <thead>
    <tr>
      <th style="text-align: right;" data-toggle="tooltip" data-placement="top" title="Positie">#</th>
      <th>Team</th>
      <th data-toggle="tooltip" data-placement="top" title="Vlaggen in bezit"><i class="icon-flag"></i></th>
      <th data-toggle="tooltip" data-placement="top" title="Veroveringen"><i class="icon-flag-alt"></i></th>
      <th data-toggle="tooltip" data-placement="top" title="Score"><i class="icon-trophy"></i></th>
    </tr>
    </thead>
    <tbody id="score-table">
    
    </tbody>
  </table>
</div>





<!-- Optional JavaScript -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-Piv4xVNRyMGpqkS2by6br4gNJ7DXjqk09RmUpJ8jgGtD7zP9yug3goQfGII0yAns" crossorigin="anonymous"></script>
<script src="js/timelapse.js?<?= rand(); ?>" type="application/javascript"></script>
<script src="js/fontawesome-markers.min.js"
        type="application/javascript"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBbz1XslmSRk6-m3gkMlxQ_Y2f_uMttTH8&callback=initMap"
        async defer></script>

<script type="application/javascript">  
  init();

  setTimeout(function(){ 
      $('#togglePlay').trigger('click');
   }, 5000);
</script>
</body>
</html>
