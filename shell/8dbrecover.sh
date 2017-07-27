#!/bin/bash
dbdir="/mysql_back"
backupdir="/vagrant/dbbackup"

if [ ! -e $dbdir ]; then
    mkdir $dbdir
fi

i=1
echo ------------------------------------------------------
file="$backupdir/*"
for filepath in ${file}
do
  if [ -d ${filepath} ]; then
    dir[$i]=${filepath}
    echo "[$i]:${filepath/${backupdir}\//}"
    let i++
  fi
done
echo ------------------------------------------------------

while read -p "リストアする対象を選んでね:" num ; do
  if [ $num -lt $i ]; then

    project=${dir[$num]/${backupdir}\//}
    echo $dbdir/$project

    # if [ ! -e $dbdir/$project ]; then
    #   echo "ごめんバグった"
    #   exit
    # fi

    echo ------------------------------------------------------
    j=1
    file="${dir[$num]}/*"
    for filepath in ${file}
    do
      if [ -d ${filepath} ]; then
        dir2[$j]=${filepath}
        echo "[$j]:${filepath/${dir[$num]}\//}"
        let j++
      fi
    done
    echo ------------------------------------------------------

    while read -p "対象日付を選んでね:" num ; do
      if [ $num -lt $j ]; then
        rm -rf $dbdir/$project
        cp -r ${dir2[$num]}/$project/ $dbdir/$project
        echo ${dir2[$num]}のデータになりました
        exit
      else
        echo ん？
      fi

    done
  else
    echo ん？
  fi
done
