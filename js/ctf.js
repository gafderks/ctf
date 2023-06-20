var showRadius = false;
var transactionInProgress = false;
var map;
var selfMarker;
var markers = [];
var polygons = [];
var balloons = [];
var locations = [];
var activeBalloon;

const weekday = ['zo', 'ma', 'di', 'wo', 'do', 'vr', 'za'];
const maxDistance = 0.060; // 60 meter

function initMap() {
  // Gets called from maps js
  updateLocations(function () {
    console.log(locations);
    // Init the map
    map = new google.maps.Map(document.getElementById('map'), {
      center: { lat: locations[0].location.lat, lng: locations[0].location.lon },
      zoom: 15,
      clickableIcons: false,
      streetViewControl: false,
      fullscreenControl: false,
      styles: [
        {
          featureType: "poi",
          stylers: [
            { visibility: "off" }
          ]
        }
      ]
    });

    // Place the markers
    for (let i = 0; i < locations.length; i++) {

      if (locations[i].polygon !== undefined) {
        polygons[i] = new google.maps.Polygon({
          paths: locations[i].polygon,
          map: map,
          strokeWeight: 1,
        });
      }
      markers[i] = new google.maps.Marker({
        position: { lat: locations[i].location.lat, lng: locations[i].location.lon },
        map: map,
      });

      balloons[i] = new google.maps.InfoWindow();
      google.maps.event.addListener(markers[i], 'click', function () {
        balloons[i].setContent(generateBalloonContent(locations[i]));
        if (activeBalloon) { activeBalloon.close(); }
        balloons[i].open(map, markers[i]);
        activeBalloon = balloons[i];
      });

      if (showRadius) {
        new google.maps.Circle({
          center: markers[i].position,
          radius: maxDistance * 1000,
          strokeColor: "#ff0000",
          map: map,
        });
      }
    
    }

    selfMarker = new google.maps.Marker({
      position: { lat: locations[0].location.lat, lng: locations[0].location.lon },
      map: map,
      icon: {
        path: fontawesome.markers.CIRCLE,
        scale: 0.3,
        strokeWeight: 0,
        strokeColor: 'black',
        strokeOpacity: 0,
        fillColor: "#006eff",
        fillOpacity: 1,
      },
    });

  });

}

function updateMarkers() {
  if (markers.length > 0) {
    if (locations.length !== markers.length) {
      location.reload();
    }

    for (let i = 0; i < locations.length; i++) {
      markers[i].setIcon({
        path: fontawesome.markers.FLAG,
        scale: 0.5,
        strokeWeight: 0.2,
        strokeColor: 'black',
        strokeOpacity: 1,
        fillColor: locations[i].color,
        fillOpacity: 1,
        labelOrigin: new google.maps.Point(35.5, -37)
      });
      markers[i].setLabel({
        text: locations[i].score,
        className: 'markerlabel'
      });
      if (locations[i].polygon !== undefined) {
        polygons[i].setOptions({
          fillColor: locations[i].color,
          strokeColor: locations[i].color === 'white' ? 'black' : locations[i].color,
        });
      }
    }
  }
}

function init() {
  bindNavigationButtons();
  logIn();
  updateAll(null);
  $('[data-toggle="tooltip"]').tooltip();
  console.log("initialized");
  setInterval(function () {
    if (!transactionInProgress) {
      updateAll(null);
    }
  }, 5000);
  setInterval(function () {
    if (!transactionInProgress) {
      locate();
    }
  }, 2000);
}

function logIn(callback) {
  if (localStorage.getItem("user") == null) {
    localStorage.setItem("visited", JSON.stringify([]));
    let name = "";
    while (name.trim() === "" || name === null) {
      name = prompt("Vul je teamnaam in");
    }
    $.ajax({
      type: "POST",
      url: "api.php?action=create&resource=team",
      data: { 'name': name }
    }).done(function (callback) { updateAll(callback); });
    localStorage.setItem("user", name);
  }
}

function bindNavigationButtons() {
  $("[data-page]").click(function () {
    $(".page").addClass("d-none");
    $("#" + $(this).data("page")).removeClass("d-none");
    $("[data-page]").parent().removeClass("active");
    $(this).parent().addClass("active");
    $(this).parent().parent().parent().removeClass("show");
  });
}

function fetchAll(callback) {
  updateNotifications(function () {
    updateLocations(function () {
      updateScoreTable(callback);
    })
  });
}

function updateAll(callback) {
  fetchAll(function () {
    locate();
    if (typeof callback === 'function') {
      callback();
    }
  });
}

function locate() {
  navigator.geolocation.getCurrentPosition(checkIfNearLocation);
}

function updateLocations(callback) {
  $.get("api.php?action=list&resource=locations&rand=" + Math.random(), function (
    data) {
    // parse polygon json
    data = data.map(loc => {
      if (loc.polygon !== undefined) {
        loc.polygon = JSON.parse(loc.polygon);
      }
      return loc;
    });
    locations = data;
    updateMarkers();
    if (typeof callback === 'function') {
      callback();
    }
  });
}

function updateNotifications(callback) {
  $.get("api.php?action=list&resource=notifications&rand=" + Math.random(), function (
    data) {
    notifications = data;
    displayNotifications(notifications);
    if (typeof callback === 'function') {
      callback();
    }
  });
}

function displayNotifications(notifications) {
  showAlert(null);
  for (let i = 0; i < notifications.length; i++) {
    showAlert(notifications[i]);
  }
}

function updateScoreTable(callback) {
  $.get("api.php?action=list&resource=scores&rand=" + Math.random(), function (
    data) {
    $("#score-table").html("");
    for (let i = 0; i < data.length; i++) {
      $("#score-table").append(
        `<tr>
          <td class="text-right">${i + 1}.</td>
          <td class="text-left"><i class="icon-sign-blank" style="color: ${data[i].color}"></i>&nbsp;${data[i].name}</td>
          <td>${data[i].captures}</td>
          <td>${data[i].total_captures}</td>
          <td class="font-weight-bold">${data[i].score}</td>
        </tr>`
      );
      if (localStorage.getItem("user") == data[i].name) {
        $("#team_name").text(data[i].name);
        $("#team_color").css("color", data[i].color);
        $(".color-flag").css("fill", data[i].color);
      }
    }
    if (typeof callback === 'function') {
      callback();
    }
  });

}

function checkIfNearLocation(location) {
  selfMarker.setPosition({ lat: location.coords.latitude, lng: location.coords.longitude });
  for (let i = 0; i < locations.length; i++) {
    let distance = getDistanceFromLatLonInKm(
      location.coords.latitude,
      location.coords.longitude,
      locations[i].location.lat,
      locations[i].location.lon
    );
    if (distance <= maxDistance) { // 30 meter
      let visited = JSON.parse(localStorage.getItem("visited"));
      if (visited.length === 0 || (visited[visited.length - 1] !== i)) {
        visited[visited.length] = i;
        localStorage.setItem("visited", JSON.stringify(visited));
        visitLocation(locations[i], true)
      }

      visitLocation(locations[i], false);
      return;
    }
  }
}

function capture(locationId) {
  $.ajax({
    type: "POST",
    url: "api.php?action=create&resource=capture",
    data: { 'location': locationId, 'name': localStorage.getItem('user') },
  }).done(updateAll(finishTransaction()));
}

function visitLocation(locationDetails, newlyVisited) {
  if (localStorage.getItem("user") !== locationDetails.owner && newlyVisited) {
    capture(locationDetails.id);
    $("#modal-location-name").text(locationDetails.name);
    $("#modal-location-score").text(locationDetails.score);
    $("#captureModal").modal("show");
  }
}

function getDistanceFromLatLonInKm(lat1, lon1, lat2, lon2) {
  let R = 6371; // Radius of the earth in km
  let dLat = deg2rad(lat2 - lat1);  // deg2rad below
  let dLon = deg2rad(lon2 - lon1);
  let a =
    Math.sin(dLat / 2) * Math.sin(dLat / 2) +
    Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) *
    Math.sin(dLon / 2) * Math.sin(dLon / 2)
    ;
  let c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
  let d = R * c; // Distance in km
  return d;
}

function showAlert(alrt) {
  if (alrt === null) {
    $("#alerts").empty();
  } else {
    $("#alerts").append("<div class='alert alert-" + alrt.type + "' role='alert'>"
      + alrt.text + "</div>")
  }
}

function deg2rad(deg) {
  return deg * (Math.PI / 180)
}

function startTransaction() {
  transactionInProgress = true;
}

function finishTransaction() {
  transactionInProgress = false;
}

function generateBalloonContent(location) {
  const name = "<h6>" + location.name + "</h6>"
  const score = "<strong>Waarde: " + location.score + "</strong><br>";
  let list = [];
  for (let i = location.captures.length - 1; i >= 0; i--) {
    let capture = location.captures[i];
    console.log(capture);
    let team = capture.team;
    if (i === location.captures.length - 1) {
      team = "<strong>" + team + "</strong>";
    }
    const timestamp = new Date(capture.timestamp * 1000)
    const day = weekday[timestamp.getDay()];
    const time = timestamp.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    list += `<li style="color: ${capture.color}">${team} (${day} ${time})</li>`;
  }
  if (list.length > 0) {
    heading = "<strong>Bezoekers:</strong>";
  } else {
    heading = "";
  }

  return name + score + heading + "<ul>" + list + "</ul>";
}