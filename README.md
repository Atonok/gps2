# camera_gps_python
Get data from camera and gpsd via python

Files, you need ....
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
