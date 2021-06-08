var transactionInProgress = false;
var map;
var markers = [];
var balloons = [];
var locations = [];
var activeBalloon;

var playingInterval = undefined;
var playingSpeed = 119;

let GAME_START = 1622895900;
let GAME_END = 1622904600;

const timeControl = document.getElementById('timelapse_range');

const weekday = ['zo', 'ma', 'di', 'wo', 'do', 'vr', 'za'];

function initMap() {
    // Gets called from maps js
    updateLocations(function () {
        // Init the map
        map = new google.maps.Map(document.getElementById('map'), {
            center: { lat: locations[0].location.lat, lng: locations[0].location.lon },
            zoom: 14,
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

        }

        updateMarkers(filterLocations(locations, GAME_START, GAME_START + parseInt(timeControl.value)));

    });

}

function updateMarkers(filteredLocations) {
    console.log(markers.length);
    if (markers.length > 0) {
        if (filteredLocations.length !== markers.length) {
            location.reload();
        }

        for (let i = 0; i < filteredLocations.length; i++) {
            markers[i].setIcon({
                path: fontawesome.markers.FLAG,
                scale: 0.5,
                strokeWeight: 0.2,
                strokeColor: 'black',
                strokeOpacity: 1,
                fillColor: filteredLocations[i].color,
                fillOpacity: 1,
                labelOrigin: new google.maps.Point(35.5, -37)
            });
            markers[i].setLabel({
                text: filteredLocations[i].score,
                className: 'markerlabel'
            });
        }
    }
}

const filterLocations = (locations, begin, end) => {
    const clone = (item) => JSON.parse(JSON.stringify(item));
    const filteredLocations = clone(locations);
    filteredLocations.forEach((loc, i) => {
        filteredLocations[i].captures = loc.captures.filter(capture => {
            return capture.timestamp >= begin && capture.timestamp <= end
        }).sort((a, b) => b.timestamp - a.timestamp);
        if (filteredLocations[i].captures.length > 0) {
            filteredLocations[i].color = filteredLocations[i].captures[filteredLocations[i].captures.length - 1].color;
        } else {
            filteredLocations[i].color = '#fff';
        }
    });
    return filteredLocations;
}

function init() {
    $('[data-toggle="tooltip"]').tooltip();
    updateLocations(() => {
        const theFilteredLocations = filterLocations(locations, GAME_START, GAME_START + parseInt(timeControl.value));
        updateMarkers(filterLocations(locations, GAME_START, GAME_START + parseInt(timeControl.value)));
        updateScoreTable(filterLocations(locations, GAME_START, GAME_START + parseInt(timeControl.value)));

        setClock(new Date(GAME_START * 1000));
        setDigitalTime(new Date(GAME_START * 1000));

        // locations.forEach(location => {
        //     location.captures.forEach(capture => {
        //         capture.location = location;
        //     })
        // });

        //const captures = locations.map(location => location.captures).flat().sort((a, b) => a.timestamp < b.timestamp);
        //GAME_START = captures[0].timestamp;
        //GAME_END = captures[captures.length - 1].timestamp;

        console.log(GAME_END - GAME_START);


        timeControl.setAttribute('max', GAME_END - GAME_START);

        timeControl.addEventListener('input', () => {
            const timestamp = new Date((parseInt(timeControl.value) + GAME_START) * 1000)
            const theFilteredLocations = filterLocations(locations, GAME_START, GAME_START + parseInt(timeControl.value));
            updateScoreTable(filterLocations(locations, GAME_START, GAME_START + parseInt(timeControl.value)));
            updateMarkers(filterLocations(locations, GAME_START, GAME_START + parseInt(timeControl.value)));
            setClock(timestamp);
            setDigitalTime(timestamp);
        });

        document.getElementById('togglePlay').addEventListener('click', () => {
            if (playingInterval === undefined) {
                // start play
                if (parseInt(timeControl.value) == parseInt(timeControl.getAttribute('max'))) {
                    timeControl.value = 0;
                }
                playingInterval = setInterval(() => {
                    if (parseInt(timeControl.value) + playingSpeed > parseInt(timeControl.getAttribute('max'))) {
                        timeControl.value = timeControl.getAttribute('max');
                        clearInterval(playingInterval);
                        document.getElementById('togglePlay').innerHTML = '<i class="icon-play"></i>';
                    } else {
                        timeControl.value = parseInt(timeControl.value) + playingSpeed;
                    }
                    timeControl.dispatchEvent(new Event('input'));
                }, 1000);
                document.getElementById('togglePlay').innerHTML = '<i class="icon-pause"></i>';
            } else {
                // stop play
                clearInterval(playingInterval);
                playingInterval = undefined;
                document.getElementById('togglePlay').innerHTML = '<i class="icon-play"></i>';
            }
        });


    });
}

function updateLocations(callback) {
    $.get("api.php?action=list&resource=locations&rand=" + Math.random(), function (
        data) {
        locations = data;
        if (typeof callback === 'function') {
            callback();
        }
    });
}

function updateScoreTable(filteredLocations) {
    const captures = filteredLocations.map(location => location.captures).flat();//.sort((a, b) => a.timestamp < b.timestamp);
    const teams = [... new Set(captures.map(capture => capture.team))];

    data = teams.map(team => {
        return {
            total_captures: captures.filter(capture => capture.team === team).length,
            captures: filteredLocations.map(location => location.captures[0]).filter(capture => capture !== undefined).filter(capture => capture.team === team).length,
            score: filteredLocations.map(location => {
                return {
                    score: location.score,
                    capture: location.captures[0]
                }
            }).filter(loc => loc.capture !== undefined).filter(loc => loc.capture.team === team).map(loc => parseInt(loc.score)).reduce((a, b) => a + b, 0),
            //score: filteredLocations.filter(location => location.owner === team).map(location => parseInt(location.score)).reduce((a, b) => a + b, 0),
            color: captures.filter(capture => capture.team === team)[0].color,
            name: team
        };
    });

    data.sort((a, b) => b.score - a.score);



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

    }


}

function generateBalloonContent(location) {
    const name = "<h6>" + location.name + "</h6>"
    const score = "<strong>Waarde: " + location.score + "</strong><br>";
    let list = [];
    for (let i = location.captures.length - 1; i >= 0; i--) {
        let capture = location.captures[i];
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

var clockI = 0;

function setClock(time) {
    //get time since midnight in milliseconds
    var now = new Date(time),
        then = new Date(
            now.getFullYear(),
            now.getMonth(),
            now.getDate(),
            0, 0, 0),
        mil = now.getTime() - then.getTime(); // difference in milliseconds

    var h = (mil / (1000 * 60 * 60));
    var m = (h * 60);
    var s = (m * 60);
    //console.log(h+":"+m+":"+s);   


    var sdegree = (s * 6);
    var srotate = "rotate(" + sdegree + "deg)";
    $("#sec").css({ "transform": srotate });

    var hdegree = h * 30 + (h / 2);
    var hrotate = "rotate(" + hdegree + "deg)";
    $("#hour").css({ "transform": hrotate });

    var mdegree = m * 6;
    var mrotate = "rotate(" + mdegree + "deg)";
    $("#min").css({ "transform": mrotate });

    if (clockI > 0) {
        $("#clock").addClass("transform");
    }
    clockI++;

}

function setDigitalTime(timestamp) {
    const day = weekday[timestamp.getDay()];
    const time = timestamp.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    document.getElementById('currentTime').textContent = `${day} ${time}`;
}