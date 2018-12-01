#!/bin/bash

SLEEPTIME=5
SLEEPTIME_MORE=10
TASK=''
Local_path='/var/Exchange/'
FILE_UNLOAD='report_6.txt'
FILE_FLAG='report_6.flr'
STORE_CASH='2'
SHOP='6'
GOODS_FILE='goods.txt'
GOODS_FLAG='goods.flz'
UPLOADLOG_FILE='upload.log'
URL="dev.shopmate.yadadya.net/dnc/"

until [ 1 = 2 ]
do
	if [ -f $Local_path$UPLOADLOG_FILE ]; then
		curl -F upload=@$Local_path$UPLOADLOG_FILE -F press=OK -F cmd=rm_goods -F shop=$SHOP -F store_cash=$STORE_CASH $URL"task.php"
		rm $Local_path$UPLOADLOG_FILE
	fi

	TASK=$(curl $URL"task.php?shop=$SHOP&store_cash=$STORE_CASH")

	if [ "$TASK" = "RELOAD_DB" ]; then
		wget -P $Local_path $URL"task.php?shop=$SHOP&store_cash=$STORE_CASH&cmd=reload_db"
		mv $Local_path"task.php?shop=$SHOP&store_cash=$STORE_CASH&cmd=reload_db" $Local_path$GOODS_FILE
		sleep $SLEEPTIME
		touch $Local_path$GOODS_FLAG
	fi

	if [ -f $Local_path$FILE_UNLOAD ]; then
		array=(`find /var/Exchange/ -type f -name $FILE_UNLOAD`)
		for i in "${array[@]}"
		do :
			curl -F upload=@$i -F press=OK $URL
			rm $i
		done
	else
		if [ -f $Local_path$FILE_FLAG ]; then
			sleep $SLEEPTIME_MORE
		else
			touch $Local_path$FILE_FLAG
		fi
	fi
	sleep $SLEEPTIME
done