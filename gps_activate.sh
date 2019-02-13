#!/bin/bash
#stty -F /dev/ttyS0 -echo
gpsd /dev/ttyS0 -nb -S 9999 -G -F /var/run/gpsd.sock
