#!/usr/bin/python -tt

import datetime as dt
import gps
import os

os.system("if [ `ps -ef | grep \"python /root/scripts/get_only_gps.py > /dev/null\" | grep -v grep | wc -l` -gt "5" ]; then  for i in `ps -ef | grep \"python /root/scripts/get_only_gps.py\" | grep -v grep | awk {'print $2'}`; do kill -9 $i; done; fi")

session = gps.gps("localhost", "9999")
session.stream(gps.WATCH_ENABLE | gps.WATCH_NEWSTYLE)

while True:
 try:
  report = session.next()
  if report['class'] == 'SKY':          # Reportuje pocet satelitov
   if hasattr(report, 'satellites'):
    num_sats = 0
    for satellite in report.satellites:
     if hasattr(satellite, 'used') and satellite.used:
      num_sats += 1

  if report['class'] == 'TPV':          # Reportuje lat, lon, alt, track, speed
   if hasattr(report, 'time'):
    if hasattr(report, 'lat'):
     lat = report.lat
    else:
     lat = 0.000000

    if hasattr(report, 'lon'):
     lon = report.lon
    else:
     lon = 0.000000

    if hasattr(report, 'alt'):
     alt = report.alt
    else:
     alt = 0

    if hasattr(report, 'track'):
     track = report.track
    else:
     track = 0

    if hasattr(report, 'speed'):
     speed = report.speed
    else:
     speed = 0

    if hasattr(report, 'climb'):
     climb = report.climb
    else:
     climb = 0

    if hasattr(report, 'mode'):
     mode = report.mode
    else:
     mode = 0

#    print("Lat: "+str(round(lat,6))+"; Lon: "+str(round(lon,6))+"; Alt: "+str(round(alt,0))+"; Time: "+report.time+"; Speed: "+str(round(speed))+"; Track: "+str(round(track))+"; Climb: "+str(round(climb,0))+"; Mode: "+str(round(mode,0))+"; Sat: "+str(num_sats)+"\n")

    f= open("/video/{}.gpx".format(dt.datetime.now().strftime("%Y%m%d")),"a+") #yyyymmdd
    f.write("<trkpt lat=\""+str(round(lat,8))+"\" lon=\""+str(round(lon,8))+"\"><ele>"+str(round(alt,0))+"</ele><time>"+report.time+"</time><speed>"+str(round(speed))+"</speed><src>gps</src><sat>"+str(num_sats)+"</sat></trkpt>\n")
    f.close()
    os.system("/usr/bin/curl \"http://gps.xxxxxxxx.sk/?lat="+str(round(lat,8))+"&lon="+str(round(lon,8))+"&time="+report.time+"&spd="+str(round(speed))+"&sat="+str(num_sats)+"&alt="+str(round(alt,0))+"&bat=100.0&acc=10.0&provider=gps&direction="+str(round(track))+"&device=KE978IE\" -o /dev/null")
   break

 except KeyError:
  pass
 except KeyboardInterrupt:
  quit()
 except StopIteration:
  session = None
  print("GPSD has terminated")
