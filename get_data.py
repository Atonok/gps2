#!/usr/bin/python -tt

from picamera import PiCamera
import datetime as dt
import time
import gps
import subprocess

camera = PiCamera()
#camera.resolution = (1920, 1080)       #1186MB/10min (8GB= 69min = 1h  6m, 16GB= 138min = 2h 12m, 32GB= 276min = 4h 24min)
#camera.resolution = (1280, 720)        # 530MB/10min (8GB=154min = 2h 34m, 16GB= 308min = 5h  8m, 32GB= 616min = 8h 48min)
#camera.resolution = (1024, 768)        # 450MB/10min (8GB=182min = 3h  3m, 16GB= 364min = 6h  6m, 32GB= 728min =12h 12min)
camera.resolution = (1024, 576) # 340MB/10min (8GB=240min = 4h    , 16GB= 480min = 8h  0m, 32GB= 960min =16h  0min)
#camera.resolution = (800, 600)         # 280MB/10min (8GB=293min = 4h 53m, 16GB= 586min = 9h 46m, 32GB=1172min =19h 32min)
#camera.resolution = (640, 480)         # 180MB/10min (8GB=455min = 7h 35m, 16GB= 960min =15h 10m, 32GB=1920min =30h 20min)
camera.rotation = 90

session = gps.gps("localhost", "9999")
session.stream(gps.WATCH_ENABLE | gps.WATCH_NEWSTYLE)
temptime_temp = 0

out = subprocess.Popen("/usr/bin/python /opt/bme280.py | tail -n4 | egrep \"Temperature|Humidity\"", shell=True, stdout=subprocess.PIPE, stderr=subprocess.STDOUT)
stdout,stderr = out.communicate()

while True:
   try:
      camera.start_preview()    # Ukaze na obrazovku
      camera.annotate_text = dt.datetime.now().strftime('%Y-%m-%d %H:%M:%S') # Zistuje aktualny cas
      camera.annotate_text_size = 20    # Vlkost textu
      timestamp = dt.datetime.now().strftime("%Y%m%d_%H%M%S")   # Vklada aktualny datum a cas
      camera.start_recording("/video/video_{}.h264".format(timestamp))  # Kam to uklada
      start = dt.datetime.now()
      lat = 0.000000    # Nulove hodnoty GPS
      lon = 0.000000
      alt = 0
      track = 0
      speed = 0
      num_sats = 0

      while (dt.datetime.now() - start).seconds < 600:  # Ak je cas mensi ako x sekund
         report = session.next()
         if report['class'] == 'SKY':           # Reportuje pocet satelitov
            if hasattr(report, 'satellites'):
               num_sats = 0
               for satellite in report.satellites:
                  if hasattr(satellite, 'used') and satellite.used:
                     num_sats += 1

         if report['class'] == 'TPV':           # Reportuje lat, lon, alt, track, speed
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

         temptime_temp += 1
         if temptime_temp == 110:       # Kazdych x sekund zisti teplotu a vlhkost
            out = subprocess.Popen("/usr/bin/python /opt/bme280.py | tail -n4 | egrep \"Temperature|Humidity\"", shell=True, stdout=subprocess.PIPE, stderr=subprocess.STDOUT)
            temptime_temp = 0
            stdout,stderr = out.communicate()

         camera.annotate_text = dt.datetime.now().strftime('%Y-%m-%d %H:%M:%S')+"\n Lat: "+str(round(lat,6))+"\n Lon: "+str(round(lon,6))+"\n Alt: "+str(round(alt,0))+" m\n Track: "+str(track)+"\n Speed: "+str(round(speed * gps.MPS_TO_KPH))+" km/h \n Satellite: "+str(num_sats)+"\n"+str(stdout)

         camera.wait_recording(0.2)
      camera.stop_recording()
      camera.stop_preview()

   except KeyError:
      pass
   except KeyboardInterrupt:
      quit()
   except StopIteration:
      session = None
      print("GPSD has terminated")
