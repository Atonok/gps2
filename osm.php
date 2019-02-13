<!DOCTYPE html>
<?php
if(isset($_GET["lat"])) $lat=$_GET["lat"];
if(isset($_GET["lon"])) $lon=$_GET["lon"];
if(isset($_GET["zoom"])) $zoom=$_GET["zoom"];
if(isset($_GET["day"])) $day=$_GET["day"];
if(isset($_GET["month"])) $month=$_GET["month"];
if(isset($_GET["year"])) $year=$_GET["year"];

if ((empty($_GET["day"])) && (empty($_GET["month"])) && (empty($_GET["year"]))) {
 $day = Date("d");
 $month = Date("m");
 $year = Date("Y");
} else {
 $day=$_GET["day"];
 $month=$_GET["month"];
 $year=$_GET["year"];
}

if ((empty($_GET["lat"])) && (empty($_GET["lon"])) && (empty($_GET["zoom"]))) {

 @$MySQL_server="localhost";
 @$MySQL_user="MySQL_user";
 @$MySQL_user_password="MySQL_password";
 @$MySQL_db="MySQL_database";
 @$MySQL_table1="MySQL_table";

 mysql_connect($MySQL_server, $MySQL_user, $MySQL_user_password);
 $spojenie=mysql_connect($MySQL_server,$MySQL_user,$MySQL_user_password);
 $spojeniedb=mysql_select_db($MySQL_db);

 $tracking_list_db = MySQL_Query("SELECT * FROM $MySQL_table1 where lat!='0.0' AND lon!='0.0' AND time like '$year-$month-$day%' order by time asc");
 $tracking_list_db_row = MySQL_numrows ($tracking_list_db);

 for ($i = 0; $i <= 1; $i++) {
  $entries = mysql_fetch_array ($tracking_list_db);
  $marker='['.$i.','.$entries['lat'].','.$entries['lon'].']';
 }

 for ($i = 1; $i <= $tracking_list_db_row; $i++) {
  $entries = mysql_fetch_array ($tracking_list_db);
  if ((!empty($entries['lat'])) && (!empty($entries['lon']))) {
   $marker=$marker.',
          ['.$i.','.$entries['lat'].','.$entries['lon'].']';
 $lat=$entries['lat'];
 $lon=$entries['lon'];
 $zoom='11';
  }
 }

 MySQL_error();
 MySQL_close();

} else {
 $lat=$_GET["lat"];
 $lon=$_GET["lon"];
 $zoom=$_GET["zoom"];
 $marker="[ \"\", $lat, $lon ]";
}

echo "<center> Lat: $lat / Lon: $lon [$day.$month.$year]";

?>
<html>
<head>
 <title> GPS logging and tracking </title>
 <meta charset="utf-8" />
 <link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet-0.7/leaflet.css" />
</head>
<body>

    <div id="map" style="height: 800px"></div>

    <script
        src="http://cdn.leafletjs.com/leaflet-0.7/leaflet.js">
    </script>

    <script>

        var markers = [
          <?php echo $marker;?>
        ];

        var map = L.map('map').setView([<?php echo $lat;?>, <?php echo $lon;?>], <?php echo $zoom;?>);
        mapLink =
            '<a href="http://openstreetmap.org">OpenStreetMap</a>';
        L.tileLayer(
            'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; ' + mapLink + ' Contributors',
            maxZoom: 18,
            }).addTo(map);

                for (var i = 0; i < markers.length; i++) {
                        marker = new L.marker([markers[i][1],markers[i][2]])
                                .bindPopup(markers[i][0])
                                .addTo(map);
                }

    </script>
</body>
</html>
