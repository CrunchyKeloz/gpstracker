<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GPS Tracker!</title>

    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link rel="stylesheet" href="custom.css">
    <style>
        #map {
        width: 100%;
        height: 100%;
        }
    </style>

    <meta name="timezone" content="America/Bogota">
</head>
<body>
  <header>
    <h1>GPS Tracker </h1>
  </header>
  <main>
    <div class="container">
        <div class="item1">
            <p id="longitude">Longitude: </p>
            <p id="latitude">Latitude: </p>
            <p id="altitude">Altitude: </p>
            <p id="date">Date: </p>
            <p id="time">Time: </p>
            <p id="timestamp">Timestamp: </p>

            <h2>Track vehicle in a given time:</h2>
            <form id="searchForm" method="post" action="getcoordinates2.php">
                <label for="startTime">Start Date & Time:</label>
                <input type="datetime-local" id="startTime" name="startTime" required><br><br>
                <label for="endTime">End Date & Time:</label>
                <input type="datetime-local" id="endTime" name="endTime" required><br><br>
                <button type="button" id="fetchButton">Fetch Route</button>
            </form>

            <div id="timestamps"></div>
            <div id="coordinates"></div>

            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script>
            $(document).ready(function() {
             $('#fetchButton').click(function() {
                var startTime = $('#startTime').val();
                var endTime = $('#endTime').val();

                var startTimestamp = Math.floor(new Date(startTime).getTime() / 1000);
                var endTimestamp = Math.floor(new Date(endTime).getTime() / 1000);

                $.ajax({
                    url: 'getcoordinates2.php',
                    method: 'POST',
                    data: {
                        startTime: startTimestamp,
                        endTime: endTimestamp
                    },
                    success: function(response) {
                        $('#timestamps').html("<p>Start Timestamp: " + startTimestamp + "</p><p>End Timestamp: " + endTimestamp + "</p>");
                        $('#coordinates').html(response);
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });
             });
            });
            </script>

        </div>
        <div class="item2">
            <div id="map"></div>
        </div>

    </div>

  </main>

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        var map = L.map('map').setView([10.983594, -74.804334], 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        var APPicon = L.icon({
            iconUrl: '/gpsmarker.png',
            iconSize: [38, 38],
            iconAnchor: [19, 38],
            popupAnchor: [0, -38]
        });

        var marker = L.marker([10.983594, -74.804334], { icon: APPicon }).addTo(map)
            .bindPopup('This is a default location. <br> Connecting to Data Base...')
            .openPopup();

        function updateMarker() {
            $.ajax({
                url: 'getcoordinates.php',
                type: 'GET',
                success: function(response){
                    console.log(response)
                    var data = JSON.parse(response);
                    let hour = data.date.split(" ");

                    function convertToTimeZone(dateString) {
                        var date = new Date(dateString);
                        return date.toLocaleTimeString('en-US', { timeZone: timezone });
                    }

                    function convertDateToTimeZone(dateString) {
                        var date = new Date(dateString);
                        return date.toLocaleDateString('en-US', { timeZone: timezone });
                    }

                    var timezone = document.querySelector('meta[name="timezone"]').getAttribute('content');

                    $("#longitude").text("Longitude: " + data.longitude);
                    $("#latitude").text("Latitude: " + data.latitude);
                    $("#altitude").text("Altitude: " + data.altitude);
                    $("#date").text("Date: " + convertDateToTimeZone(data.date));
                    $("#time").text("Time: " + convertToTimeZone(data.date));
                    $("#timestamp").text("Timestamp: " + data.timestamp);
                    var latlng = [parseFloat(data.latitude), parseFloat(data.longitude)];
                    marker.setLatLng(latlng);
                    marker.bindPopup('Latitude: ' + data.latitude + '<br>Longitude: ' + data.longitude).openPopup();
                    map.setView(latlng);
                }
            });
        }

        setInterval(updateMarker, 3000);
    </script>
</body>
</html>