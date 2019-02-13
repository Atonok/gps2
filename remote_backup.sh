#!/bin/bash
HOSTNAME=`cat /etc/hostname`
RSYNC='/usr/bin/rsync'
SSH='/usr/bin/ssh'
USER='backup_user'
HOST='xxx.xxx.xxx.xxx'
LOCDATA='/bin /boot /etc /home /lib /opt /root /sbin /selinux /usr /var'
REMDATA='/data/share/backup/SRV_'${HOSTNAME}'.xxxxxxxxxxxx.sk'
EXCLUDE=''

dpkg-query --list > /root/list_all_packages.txt
dpkg-query --list | wc -l >> /root/list_all_packages.txt

echo $RSYNC -avzrtplh --stats --delete --ignore-missing-args $EXCLUDE $LOCDATA $USER@$HOST:$REMDATA
sleep 5
$RSYNC -avzrtplh --stats --delete --ignore-missing-args $EXCLUDE $LOCDATA $USER@$HOST:$REMDATA
ssh $USER@$HOST "echo \"`date +%F`\" > $REMDATA/last_backup"
