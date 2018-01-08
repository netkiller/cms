#!/bin/sh

LOGFILE=/www/netkiller.cn/inf.netkiller.cn/log/$(basename $0 .sh).log
PATTERN="synchronous.hxpm.php"
RECOVERY="php -d error_log=/www/netkiller.cn/inf.netkiller.cn/log/php_errors.log -c /srv/php/etc/php-cli.ini /www/netkiller.cn/inf.netkiller.cn/daemon/synchronous.hxpm.php restart"

while true
do
    TIMEPOINT=$(date -d "today" +"%Y-%m-%d_%H:%M:%S")
    PROC=$(pgrep -o -f ${PATTERN})
    #echo ${PROC}
    if [ -z "${PROC}" ]; then
		${RECOVERY} >> $LOGFILE
		echo "[${TIMEPOINT}] ${PATTERN} ${RECOVERY}" >> $LOGFILE
    #else
        #echo "[${TIMEPOINT}] ${PATTERN} ${PROC}" >> $LOGFILE
    fi
sleep 5
done &
