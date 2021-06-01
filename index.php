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

  <title>Vlaggenroof</title>
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
  </style>
</head>
<body>

<div class="modal fade" id="captureModal" tabindex="-1" aria-labelledby="captureModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="captureModalLabel">Je hebt een vlag veroverd!</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <svg class="capture-image img-fluid mt-2" viewBox="0 -4 358.613 368.613">
        <path d="M286.319,209.758c0,0,13.674-8.197,13.5-23.877c-0.172-15.678-11.021-19.902-24.207-19.796l-65.9-0.014
			c-2.908-6.404-5.828-18.135-14.24-24.814c-14.982-11.895-27.471-6.413-36.889-0.785c-21.282,12.704-45.341,39.315-59.985,44.249
			c-18.007,6.067-39.806,4.486-39.806,4.486v91.684c37.328,0,60.121,13.121,79.125,20.916
			c43.066,17.662,73.184,17.358,88.932,16.772c32.658-1.215,35.4-27.87,36.285-33.175c8.475-3.372,14.045-8.527,16.6-15.344
			c1-2.654,1.506-5.56,1.506-8.604c0-5.118-1.404-9.728-2.656-12.815c7.424-4.092,12.029-9.551,13.707-16.24
			c0.465-1.838,0.678-3.779,0.699-5.774C293.144,213.062,286.319,209.758,286.319,209.758z"/>
		<path class="color-flag" stroke="black" stroke-width="8" d="M244.321,333.57l-21.393,0.002v20.041c0,1.326,0.527,2.6,1.465,3.535c0.938,0.939,2.209,1.465,3.535,1.465h11.398
			c1.326,0,2.6-0.525,3.535-1.465c0.939-0.938,1.465-2.211,1.465-3.537L244.321,333.57z"/>
		<path class="color-flag" stroke="black" stroke-width="8" d="M239.319,0h-11.391c-2.762,0-5,2.239-5,5v4.419c-5.021,1.332-10.293,1.986-15.932,1.986
			c-11.38,0-23.051-2.687-34.338-5.285c-11.609-2.672-23.613-5.435-35.67-5.435c-9.861,0-18.725,1.837-27.098,5.616
			c-0.907,0.41-1.484,1.245-1.492,2.162c-0.008,0.917,0.554,1.76,1.454,2.183c14.908,6.998,26.032,19.369,36.703,31.51
			c-14.184,12.398-26.554,26.513-37.761,43.079c-0.628,0.928-0.5,2.12,0.314,2.92c0.813,0.802,2.116,1.02,3.193,0.533
			c7.607-3.433,15.682-5.103,24.687-5.103c11.381,0,23.052,2.687,34.339,5.284c11.608,2.672,23.612,5.435,35.669,5.435
      c5.604,0,10.878-0.615,15.932-1.821v58.488h21.391V5C244.319,2.24,242.081,0,239.319,0z"/>
      <text id="modal-location-score" x="160" y="60" fill="black" style="font-size: 42px;" class="markerlabel">75</text>
        </svg>
        <h3 id="modal-location-name" style="text-align: center;">Volckaert</h3>
      </div> 
      <div class="modal-footer" style="justify-content: center; border-top: 0;">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Sluiten</button>
      </div>
    </div>
  </div>
</div>

<div id="alerts">
</div>
<nav class="navbar navbar-expand-xs navbar-light bg-light">
  <a class="navbar-brand" href="#">Vlaggenroof</a>

  <div class="ml-auto mr-3" data-toggle="tooltip" data-placement="bottom" title="Je teamkleur en -naam">
    <i id="team_color" class="icon-flag"></i>
    <span id="team_name"></span>
  </div>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="navbarSupportedContent">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item active">
        <a class="nav-link" data-page="page-map" href="#">Kaart  <span
              class="sr-only">
            (current)</span></a>
      </li>
      <li class="nav-item">
        <a class="nav-link" data-page="page-ownership" href="#">Scores</a>
      </li>

      
<!--      <li class="nav-item">-->
<!--        <a class="nav-link" href="javascript:localStorage.clear();location.reload();">Uitloggen</a>-->
<!--      </li>-->
    </ul>
  </div>
</nav>

<div id="page-map" class="page" style="height: calc(100% - 56px)">
  <div id="map"></div>
</div>

<div id="page-ownership" class="page container d-none">
  <table class="table table-responsive">
    <thead>
    <tr>
      <th data-toggle="tooltip" data-placement="top" title="Positie">#</th>
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
<script src="js/ctf.js?<?= rand(); ?>" type="application/javascript"></script>
<script src="js/fontawesome-markers.min.js"
        type="application/javascript"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBbz1XslmSRk6-m3gkMlxQ_Y2f_uMttTH8&callback=initMap"
        async defer></script>

<script type="application/javascript">  
  init();
</script>
</body>
</html>
