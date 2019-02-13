<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
 <title> GPS logging and tracking </title>
 <meta http-equiv="Expires" CONTENT="Sun, 12 May 2003 00:36:05 GMT">
 <meta http-equiv="Pragma" CONTENT="no-cache">
 <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
 <meta http-equiv="Cache-control" content="no-cache">
 <meta http-equiv="Content-Language" content="sk">
 <meta http-equiv="refresh" content="35">
 <meta name="GOOGLEBOT" CONTENT="noodp">
 <meta name="pagerank" content="10">
 <meta name="msnbot" content="robots-terms">
 <meta name="revisit-after" content="2 days">
 <meta name="robots" CONTENT="index, follow">
 <meta name="alexa" content="100">
 <meta name="distribution" content="Global">
</head>
<body>

<?php
$starttime = explode(' ', microtime());
$starttime = $starttime[1] + $starttime[0];

@$MySQL_server="localhost";
@$MySQL_user="MySQL_user";
@$MySQL_user_password="MySQL_password";
@$MySQL_db="MySQL_database";
@$MySQL_table1="MySQL_table";

mysql_connect($MySQL_server, $MySQL_user, $MySQL_user_password);
$spojenie=mysql_connect($MySQL_server,$MySQL_user,$MySQL_user_password);
$spojeniedb=mysql_select_db($MySQL_db);


$ip=$_SERVER["REMOTE_ADDR"];


if (empty($_GET["day"])) $day=Date("d");
if(isset($_GET["day"])) $day=$_GET["day"];

if (empty($_GET["month"])) $month=Date("m");
if(isset($_GET["month"])) $month=$_GET["month"];

if (empty($_GET["year"])) $year=Date("Y");
if(isset($_GET["year"])) $year=$_GET["year"];

if (empty($_GET["hour"])) $hour=Date("H");
if(isset($_GET["hour"])) $hour=$_GET["hour"];

if (empty($_GET["minute"])) $minute=Date("i");
if(isset($_GET["minute"])) $minute=$_GET["minute"];

if (empty($_GET["second"])) $second=Date("s");
if(isset($_GET["second"])) $second=$_GET["second"];

if(isset($_GET["lat"])) $lat=$_GET["lat"];
if(isset($_GET["lon"])) $lon=$_GET["lon"];
if(isset($_GET["alt"])) $alt=$_GET["alt"];
if(isset($_GET["acc"])) $acc=$_GET["acc"];
if(isset($_GET["spd"])) $spd=$_GET["spd"];
if(isset($_GET["sat"])) $sat=$_GET["sat"];
if(isset($_GET["bat"])) $bat=$_GET["bat"];
if(isset($_GET["time"])) $time=$_GET["time"];
if(isset($_GET["device"])) $device=$_GET["device"];
if(isset($_GET["provider"])) $provider=$_GET["provider"];
if(isset($_GET["direction"])) $direction=$_GET["direction"];

if (!empty($_GET["time"])) {
  $timeT=str_replace("T","\T",$time);
  $timeZ=str_replace("Z","\Z",$timeT);
  $epoch= strtotime (gmdate($timeZ));
  $time=date ('Y-m-d\TH:i:s\Z',$epoch);
  MySQL_Query("INSERT INTO $MySQL_table1 VALUES('','$lat','$lon','$alt','$acc','$spd','$sat','$time','$bat','$ip','$year','$month','$day','$hour','$minute','$second','$device','$provider','$direction')");

//// Sem ide kod, ktory sa vykona, ked zariadenie posle svoju lokalizaciu.
//// Napr: odosle sa mail na zaklade zadanych kriterii (suradnice, nadmorska vyska, stav baterie, ...)
 $send_mail = "N";

if ($bat < "90") {
 $subject = "GPS - Notification - Battery ".$bat." %";
# $send_mail = "Y";
}

if ($send_mail == "Y") {
 $headers = "From: webmaster@xxxxxxxxxx.sk" . "\r\n";
 $to = "xxxxx@xxxxxxxxxx.sk";
 $txt = "Last position: http://gps.xxxxxxxxxx.sk/osm.php?lat=".$lat."&lon=".$lon."&zoom=15 \r\n ".$lat." / ".$lon." (".$alt.") \r\n via ".$provider." \r\n from ".$ip." \r\n at ".$time;
 mail($to,$subject,$txt,$headers);
}
} else {

#### KALENDAR
 $first_day = mktime(0,0,0,$month, 0, $year) ;
 $title = date('F', $first_day) ;
 $day_of_week = date('D', $first_day) ;

 switch($day_of_week){
  case "Mon": $blank = 1; break;
  case "Tue": $blank = 2; break;
  case "Wed": $blank = 3; break;
  case "Thu": $blank = 4; break;
  case "Fri": $blank = 5; break;
  case "Sat": $blank = 6; break;
  case "Sun": $blank = 7; break;
 }
 //We then determine how many days are in the current month
 $days_in_month = cal_days_in_month(1, $month, $year) ;
 $prev_month = $month-1;
 $next_month = $month+1;
 $yearnow = $year;

 if (($prev_month < 10) and ($prev_month > 0)) {$prev_month = '0'.$prev_month; }
 if (($next_month < 10) and ($next_month > 0)) {$next_month = '0'.$next_month; }

 if ($month == 12) {$next_month = '01'; }#$year = $year-1; }
 if ($month == 01) {$prev_month = '12'; }#$year = $year+1; }

 $bgsob = '#aaaaaa';
 $bgned = '#7a7a52';
 $bgday = '#fffff0';

 echo "<table border=\"1\" width=\"364\" align=\"center\">";
 echo "<tr><th colspan=\"7\" bgcolor=\".$bgday.\"><a href='?year=$year&amp;month=$prev_month'> <<<--- </a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; $month $year &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href='?year=$year&amp;month=$next_month'> --->>></a><br>";
 echo "<a href='?'>".Date("d").".".Date("m").".".Date("Y")." - ".$hour.":".$minute.":".$second."</a><br>";
 echo "<a href='osm.php?year=$year&amp;month=$month&amp;day=$day'>Summary</a></th></tr>";
 echo "<tr><td width=\"52\" bgcolor=\".$bgday.\"><b>Pon</b></td><td width=\"52\" bgcolor=\".$bgday.\"><b>Uto</b></td><td width=\"52\" bgcolor=\".$bgday.\"><b>Str</b></td><td width=\"52\" bgcolor=\".$bgday.\"><b>Stv</b></td><td width=\"52\" bgcolor=\".$bgday.\"><b>Pia</b></td><td width=\"52\" bgcolor=\".$bgsob.\"><b>Sob</b></td><td width=\"52\" bgcolor=\".$bgned.\"><b><i>Ned</i></b></td></tr>";

 $day_count = 1;
 echo "<tr>";
 while ( $blank > 0 )
 {
  echo "<td></td>";
  $blank = $blank-1;
  $day_count++;
 }
 $day_num = 1;
 while ( $day_num <= $days_in_month )
 {
  if ($day_num < 10) {
   $day_num = '0'.$day_num;
  }

  if ($day_count == '8') {$bgcol = $bgned;}
  if ($day_count == '7') {$bgcol = $bgned;}
  if ($day_count == '6') {$bgcol = $bgsob;}
  if ($day_count <= '5') {$bgcol = $bgday;}
  if (($day_num == $day) and ($month == $month)) {$bgcol = '#99FF99';}
  if (($day_num == Date("d")) and ($month == Date("m"))) {$bgcol = '#990099';}

  echo "<td align=center bgcolor=\"$bgcol\"> <a href='?year=$year&amp;month=$month&amp;day=$day_num'><b>$day_num</b></a> <a href='osm.php?year=$year&amp;month=$month&amp;day=$day_num' target='_blank'>*</a></td>";
  $day_num++;
  $day_count++;
  //Make sure we start a new row every week
  if ($day_count > 7)
  {
   echo "</tr><tr>";
   $day_count = 1;
  }
 }
 //Finaly we finish out the table with some blank details if needed
 while ( $day_count >1 && $day_count <=7 )
 {
  echo "<td> </td>";
  $day_count++;
 }
 echo "</tr></table>";

##### /KALENDAR

#if (($day > '0') and ($day < '10')) { $day='0'.$day;}
 $tracking_list_db = MySQL_Query("SELECT * FROM $MySQL_table1 where lat!='0.0' AND lon!='0.0' AND time like '$year-$month-$day%' order by time desc");
 $tracking_list_db_row = MySQL_numrows ($tracking_list_db);


 $WEB_HEADER="<table align=\"center\" border=\"1\" cellpadding=\"0\" cellspacing=\"0\">
<tr>
 <td bgcolor=\"#000000\"><font color=\"00FAAA\"><b>ID</b></font></td>
 <td bgcolor=\"#000000\"><font color=\"00FAAA\"><b>Lat/lon</b></font></td>
 <td bgcolor=\"#000000\"><font color=\"00FAAA\"><b>Alt</b></font></td>
 <td bgcolor=\"#000000\"><font color=\"00FAAA\"><b>Speed</b></font></td>
 <td bgcolor=\"#000000\"><font color=\"00FAAA\"><b>Direction</b></font></td>
 <td bgcolor=\"#000000\"><font color=\"00FAAA\"><b>Satelites</b></font></td>
 <td bgcolor=\"#000000\"><font color=\"00FAAA\"><b>Battery</b></font></td>
 <td bgcolor=\"#000000\"><font color=\"00FAAA\"><b>Event time</b></font></td>
 <td bgcolor=\"#000000\"><font color=\"00FAAA\"><b>IP</b></font></td>
 <td bgcolor=\"#000000\"><font color=\"00FAAA\"><b>Provider</b></font></td>
 <td bgcolor=\"#000000\"><font color=\"00FAAA\"><b>Miesto</b></font></td>
</tr>
";
 $WEB_MIDDLE="";
 $WEB_FOOTER="</table>
";

 for ($i = 1; $i <= $tracking_list_db_row; $i++) {
  $entries = mysql_fetch_array ($tracking_list_db);

  $direction='---';
  if (($entries['direction'] >   '0') and ($entries['direction'] <  '45' )) { $direction='S';}
  if (($entries['direction'] >  '46') and ($entries['direction'] < '125' )) { $direction='V';}
  if (($entries['direction'] > '126') and ($entries['direction'] < '225' )) { $direction='J';}
  if (($entries['direction'] > '226') and ($entries['direction'] < '275' )) { $direction='Z';}
  if (($entries['direction'] > '276') and ($entries['direction'] < '366' )) { $direction='S';}

  $miesto=' - - - ';
  $bgmiesto='#ffffff';
  if ($entries['lat'] > '48.767') { $miesto='KE-smer Server SK';}
  if ($entries['lat'] < '48.658') { $miesto='KE-smer Juh SK';}
  if ($entries['lon'] > '21.322') { $miesto='KE-smer Vychod SK';}
  if ($entries['lon'] < '21.189') { $miesto='KE-smer Zapad SK';}

  include ('locations.php');

  if ($entries['provider'] == 'network') { $bgmiesto='#f0f000'; }

  if ($entries['bat'] < '20') { $bgmiesto='#ff8000';}
  if ($entries['bat'] < '10') { $bgmiesto='#ff0000';}

$SPD=$entries['spd']*3.6;

$WEB_MIDDLE=$WEB_MIDDLE.' <tr>
  <td bgcolor="'.$bgmiesto.'">'.$entries['id'].'</td>
  <td bgcolor="'.$bgmiesto.'"><a href="osm.php?lat='.$entries['lat'].'&amp;lon='.$entries['lon'].'&amp;zoom=15" target="_blank">'.round($entries['lat'],4).' / '.round($entries['lon'],4).'</a></td>
  <td bgcolor="'.$bgmiesto.'">'.round($entries['alt'],3).'</td>
  <td bgcolor="'.$bgmiesto.'" >'.round($SPD,2).'</td>
  <td bgcolor="'.$bgmiesto.'" align="center">'.$direction.'</td>
  <td bgcolor="'.$bgmiesto.'" align="center">'.$entries['sat'].'</td>
  <td bgcolor="'.$bgmiesto.'" align="center">'.round($entries['bat'],0).'%</td>
  <td bgcolor="'.$bgmiesto.'">'.str_replace("T"," / ",$entries['time']).'</td>
  <td bgcolor="'.$bgmiesto.'">'.$entries['ip'].'</td>
  <td bgcolor="'.$bgmiesto.'">'.$entries['provider'].'</td>
  <td bgcolor="'.$bgmiesto.'">'.$miesto.'</td>
 </tr>
';

}
echo $WEB_HEADER.$WEB_MIDDLE.$WEB_FOOTER;
echo '<p align="center"><a href="http://validator.w3.org/check?uri=referer" target="_blank"><img src="http://www.w3.org/Icons/valid-html401" alt="Valid HTML 4.01 Transitional" height="31" width="88" border="0"></a><br>';
}

MySQL_error();
MySQL_close();

$mtime = explode(' ', microtime());
$totaltime = $mtime[0] + $mtime[1] - $starttime;
printf ('Stránka vygenerovaná za %.3f sekundy.', $totaltime);

?>

</body>
</html>
