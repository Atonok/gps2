#!/bin/bash
RUNNING=`ps -ef | grep -v grep | grep -v "ntpd_check.sh" | grep ntpd | wc -l`
echo ${RUNNING}
if [ ${RUNNING} -eq "0" ]; then
 systemctl restart ntp
 logger -p info "NTPd restarted from script monitor"
fi
