#!/bin/bash
sync_dir=/vagrant/src
adminer_dir=/vagrant/src/dd

project_name[0]=laravel
document_root[0]=$sync_dir/${project_name[0]}/blog/public
mysql_flag[0]="-v /mysql_back/"${project_name[0]}"/:/var/lib/mysql/"



i=0
for project in "${project_name[@]}"; do
  ipaddr=$((10+$i))

  docker run -d\
    -p 192.168.33.$ipaddr:80:80 \
    -p 192.168.33.$ipaddr:443:443 \
    -p 192.168.33.$ipaddr:100:100 \
    -v $sync_dir/:$sync_dir/ \
    -v $adminer_dir:$adminer_dir \
    --name $project \
    --privileged \
    ${mysql_flag[i]} \
    -e PATH=/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin:vagrant/src/laravel/vendor/bin \
    -it centos-lamp:6.6.1 /bin/bash

  #apacheのwelcomeページ消す
  docker exec ${project_name[i]} sed -i -e '7,10d' /etc/httpd/conf.d/welcome.conf

  docker exec ${project_name[i]} rm -rf /var/www/html
  docker exec ${project_name[i]} ln -s ${document_root[i]} /var/www/html

  if [ -n "${mysql_flag[i]}" ] ; then
    docker exec ${project_name[i]} chown -R mysql:mysql /var/lib/mysql
    docker exec ${project_name[i]} service mysqld start

    docker exec ${project_name[i]} sed -i -e '$a\
Alias /dd/ "'$adminer_dir'/"\
<Directory "'$adminer_dir'/">\
    Allow from all\
</Directory>' /etc/httpd/conf/httpd.conf
  fi

  docker exec ${project_name[i]} service httpd start
  docker exec ${project_name[i]} service postfix start

  docker exec ${project_name[i]} ip addr add 192.168.0.$ipaddr/24 dev eth0

  let i++
done
