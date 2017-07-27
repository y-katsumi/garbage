#!/bin/bash
today=`date +"%Y%m%d"`
dbdir="/mysql_back"
backupdir="/vagrant/dbbackup"

i=1
echo ------------------------------------------------------
file="$dbdir/*"
for filepath in ${file}
do
  if [ -d ${filepath} ]; then
    dir[$i]=${filepath/$dbdir\//}
    echo "[$i]:${dir[$i]}"
    let i++
  fi
done
echo ------------------------------------------------------

while read -p "バックアップする対象を選んでね:" num ; do
  if [ $num -lt $i ]; then
    if [ -e $backupdir/${dir[$num]}/$today/ ]; then
      echo $backupdir/${dir[$num]}/$today/
      read -p "ファイル上書きしちゃうけど良い？[yn]:" Yn;
      if [ ${Yn,,} != "y" ]; then
        continue
      fi
    fi

    rm -rf $backupdir/${dir[$num]}/$today/
    mkdir -p $backupdir/${dir[$num]}/$today/
    sudo cp -r $dbdir/${dir[$num]} $backupdir/${dir[$num]}/$today/ 2> $backupdir/${dir[$num]}/$today/dbbackup.log &
    echo 完了です。
    exit

  else
    echo ん？
  fi
done
