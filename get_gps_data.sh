#!/bin/bash
DIR='/video'

# Checking consumed size of captured video from camera
CONSUMED=`df -h ${DIR} | awk {'print $5'} | tr -d '%' | tail -n1`
 if [ ${CONSUMED} -gt '90' ]; then
  FILES=`ls -tr ${DIR} | grep -v txt | head -1`
  for FILE in ${FILES}; do
   rm -rf ${DIR}/${FILE}
   logger -p info "Deleted file ${FILE}"
  done
 fi


# Check if physical pin27 is +5V.
# If it is 0 (not Ucc here), it will kill camera process.
# If it is 1 (is there Ucc), it will check if camera is recording.
#  If not, start the camera recording.

STATUS=`gpio read 1`;
if [ $STATUS == '0' ]; then
 if [ `ps -ef | grep -v grep | grep get_data | grep -v vi | wc -l` -ne '0' ]; then
  /bin/kill -9 `ps -ef | grep -v grep | grep get_data | grep -v vi | awk {'print $2'}`
 fi
else
 RUNNING=`ps -ef | grep -v grep | grep -v "get_gps_data.sh" | grep get_data.py | wc -l`
 echo ${RUNNING}
 if [ ${RUNNING} -eq "0" ]; then
 python /root/scripts/get_data.py &
  logger -p info "GET_DATA restarted from script monitor"
 fi
fi
