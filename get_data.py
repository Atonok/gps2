#!/usr/bin/python -tt

from picamera import PiCamera
import datetime as dt
import time
import gps
import subprocess

camera = PiCamera()
#camera.resolution = (1920, 1080)	#1186MB/10min (8GB= 69min = 1h  6m)
#camera.resolution = (1280, 720)	# 530MB/10min (8GB=154min = 2h 34m)
#camera.resolution = (1024, 768)	# 450MB/10min (8GB=182min = 3h  3m)
camera.resolution = (1024, 576)	# 340MB/10min (8GB=240min = 4h    )
#camera.resolution = (800, 600)		# 280MB/10min (8GB=293min = 4h 53m)
#camera.resolution = (640, 480)		# 180MB/10min (8GB=455min = 7h 35m)
camera.rotation = 180

session = gps.gps("localhost", "9999")
session.stream(gps.WATCH_ENABLE | gps.WATCH_NEWSTYLE)
temptime = 0

out = subprocess.Popen("/usr/bin/python /opt/bme280.py | head -n1", shell=True, stdout=subprocess.PIPE, stderr=subprocess.STDOUT)
stdout,stderr = out.communicate()

while True:
   try:
      camera.start_preview()
      camera.annotate_text = dt.datetime.now().strftime('%Y-%m-%d %H:%M:%S')
      camera.annotate_text_size = 20
      timestamp = dt.datetime.now().strftime("%Y%m%d_%H%M%S")
      camera.start_recording("/video/video_{}.h264".format(timestamp))
      start = dt.datetime.now()
      while (dt.datetime.now() - start).seconds < 600:
         report = session.next()
         if report['class'] == 'TPV':
            if hasattr(report, 'time'):
               print(report.time)
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

               temptime += 1
               if temptime == 50:
                  out = subprocess.Popen("/usr/bin/python /opt/bme280.py | head -n1", shell=True, stdout=subprocess.PIPE, stderr=subprocess.STDOUT)
                  temptime = 0
                  stdout,stderr = out.communicate()

               camera.annotate_text = dt.datetime.now().strftime('%Y-%m-%d %H:%M:%S')+"\n Lat: "+str(round(lat,6))+"\n Lon: "+str(round(lon,6))+"\n Alt: "+str(round(alt,0))+" m\n Track: "+str(track)+"\n Speed: "+str(round(speed * gps.MPS_TO_KPH))+" km/h\n"+str(stdout)
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
