## Get data from camera and gpsd via python, and send it to the web.

### Client side (Raspberry PI)
Files, you need on RPI device ....
```
gps_activate.sh
mem_check.sh
get_only_gps.py
get_gps_data.sh
get_data.py
```
Optional (just for backup)
```
remote_backup.sh
```
Activate gps during boot, add lines to ```/etc/rc.local```
```
/root/scripts/gps_activate.sh
```

Periodic check, add lines to ```/etc/crontab```
```
# Check memory and swap status. Do "drop_caches", when limit is to low.
* *     * * *   root    /root/scripts/mem_check.sh > /dev/null 2>&1

# Get only GPS data from module. Write it to .gpx file, and try curl to website for online tracking.
* *     * * *   root    python /root/scripts/get_only_gps.py > /dev/null 2>&1

# Cleanup old video files, and get staus from GPIO pin. If 1, start video and gps capturing. If 0, stop video.
* *     * * *   root    /root/scripts/get_gps_data.sh > /dev/null 2>&1

# Upload file to datastore  
59 23   * * *   root    sleep 50; curl -T /video/`date '+%Y%m%d'`.gpx ftp://xxxxxxxxxx.sk/

# For backup
00 4    * * *   root    /root/scripts/remote_backup.sh > /var/log/remote_backup.log 2>&1
00 6    * * *   root    killall rsync > /dev/null 2>&1
```
.gpx and .h264 files are located ```/video```
```
SERVER /video # ls -l
total 27428
-rw-r--r-- 1 root root    58183 Feb  3 23:59 20190203.gpx
-rw-r--r-- 1 root root   143773 Feb  4 23:59 20190204.gpx
-rw-r--r-- 1 root root   123010 Feb  5 14:31 20190205.gpx
-rw-r--r-- 1 root root   100178 Feb  9 23:59 20190209.gpx
-rw-r--r-- 1 root root    94086 Feb 10 12:22 20190210.gpx
-rw-r--r-- 1 root root   846252 Feb 13 20:35 video_20190213_203507.h264
-rw-r--r-- 1 root root 26683996 Feb 13 20:38 video_20190213_203605.h264
```
### Server side (Linux)
```
index.php
osm.php
locations.php
database.sql
```
Optional (just for manual import .gpx)
```
manual_GPS_upload.sh
```
Import ```database.sql``` into your MySQL server.
Other files ```*.php``` upload to you web server. Remember, the device will call http://url/index.php , so you can not rename the file. Othervise, you need correct entry in ```get_only_gps.py``` line 64. still you need correct the url, set your web.

If you have shell access on server, you can set manual upload.
Add to ```/etc/crontab``` line
```10  1   * * *   user    /home/user/logging/manual_GPS_upload.sh > /dev/null 2>&1```


Thsi is related to own OpenStreetMaps server. You can follow instructions to build own here - [https://github.com/admik007/openstreetmaps]
