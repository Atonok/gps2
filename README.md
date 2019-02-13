# camera_gps_python
Get data from camera and gpsd via python

Files, you need ....
```
gps_activate.sh
mem_check.sh
get_only_gps.py
get_gps_data.sh
```
Optional (just for backup)
```
remote_backup.sh
```
Activate gps during boot, add lines to ```/etc/rc.local```
```
############ START HERE ############
/root/scripts/gps_activate.sh
############ END HERE ############
```

Periodic check, add lines to ```/etc/crontab```
```
############ START HERE ############
# Check memory and swap status. Do "drop_caches", when limit is to low.
* *     * * *   root    /root/scripts/mem_check.sh > /dev/null 2>&1
# Get only GPS data from module. Write it to .gpx file, and try curl to website for online tracking.
* *     * * *   root    python /root/scripts/get_only_gps.py > /dev/null 2>&1
## Cleanup old video files, and get staus from GPIO pin. If 1, start video and gps capturing. If 0, stop video.
* *     * * *   root    /root/scripts/get_gps_data.sh > /dev/null 2>&1
## Upload file to datastore  
59 23   * * *   root    sleep 50; curl -T /video/`date '+%Y%m%d'`.gpx ftp://xxxxxxxxxx.sk/
## For backup
00 4    * * *   root    /root/scripts/remote_backup.sh > /var/log/remote_backup.log 2>&1
00 6    * * *   root    killall rsync > /dev/null 2>&1
############ END HERE ############
```
