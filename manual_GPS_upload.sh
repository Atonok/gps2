#!/bin/bash
CESTA='`pwd`/logging/'
TODAY="`date '+%Y%m%d'`.gpx"

for FILE in `ls -r ${CESTA} | grep gpx | grep -v ${TODAY}`; do
 echo ${FILE}
 if [ ! -f ${CESTA}done/${FILE} ]; then
  cat ${CESTA}${FILE} | while read line; do
   LAT=`echo ${line} | grep trkpt | cut -d ' ' -f2 | grep lat | cut -d '"' -f2`
   LON=`echo ${line} | grep trkpt | cut -d ' ' -f3 | grep lon | cut -d '"' -f2`
   ALT=`echo ${line} | grep trkpt | cut -d '>' -f3 | grep ele | cut -d '<' -f1`
   TIM=`echo ${line} | grep trkpt | cut -d '>' -f5 | grep time | cut -d '<' -f1`
   SPD=`echo ${line} | grep trkpt | cut -d '>' -f7 | grep speed | cut -d '<' -f1`
   if [ "${SPD}" == "0.0" ]; then
    DIR=''
    SPD=`echo ${line} | grep trkpt | cut -d '>' -f7 | grep speed | cut -d '<' -f1`
    PRO=`echo ${line} | grep trkpt | cut -d '>' -f9 | grep src | cut -d '<' -f1`
    SAT=`echo ${line} | grep trkpt | cut -d '>' -f11 | grep sat | cut -d '<' -f1`
   else
    DIR=`echo ${line} | grep trkpt | cut -d '>' -f7 | grep course | cut -d '<' -f1`
    SPD=`echo ${line} | grep trkpt | cut -d '>' -f9 | grep speed | cut -d '<' -f1`
    PRO=`echo ${line} | grep trkpt | cut -d '>' -f11 | grep src | cut -d '<' -f1`
    SAT=`echo ${line} | grep trkpt | cut -d '>' -f13 | grep sat | cut -d '<' -f1`
   fi
   if [ "${LAT}" != "" ] && [ "${LON}" != "" ]; then
    curl "http://gps.xxxxxxxxxx.sk/?lat=${LAT}&lon=${LON}&time=${TIM}&spd=${SPD}&sat=${SAT}&alt=${ALT}&bat=100.0&acc=10.0&provider=${PRO}&direction=${DIR}&device=CAR_LICENCE" -o /dev/null
   fi
  done
  mv  ${CESTA}${FILE} "${CESTA}done/${FILE}"
 else
  rm -f ${FILE}
 fi
done
