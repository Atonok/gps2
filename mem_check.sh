#!/bin/bash
MT=`cat /proc/meminfo | grep "MemTotal" | awk {'print $2'}`
MF=`cat /proc/meminfo | grep "MemFree" | awk {'print $2'}`
ST=`cat /proc/meminfo | grep "SwapTotal" | awk {'print $2'}`
SF=`cat /proc/meminfo | grep "SwapFree" | awk {'print $2'}`
LIMIT="10"

if [ "${MT}" != "0" ]; then
 MEM=`echo ${MF}*100/${MT} | bc`
 MEMO=`echo "Free Memory: ${MEM} % (${MF}k of ${MT}k)"`
fi


if [ "${ST}" != "0" ]; then
 SWP=`echo ${SF}*100/${ST} | bc`
 SWAP=`echo "Free Swap: ${SWP} % (${SF}k of ${ST}k)"`
fi

logger ${MEMO} ${SWAP}
echo ${MEMO} ${SWAP}

if [ "${MEM}" -lt "${LIMIT}" ]; then
 sync; echo 3 > /proc/sys/vm/drop_caches
 logger "Memory cleaned"
fi
