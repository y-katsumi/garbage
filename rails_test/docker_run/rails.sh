#!/bin/bash

image="rails:5.1"

sync_dir=/myapp

ipaddr=10
docker run -d\
  -p 192.168.33.$ipaddr:20:20 \
  -p 192.168.33.$ipaddr:21:21 \
  -p 192.168.33.$ipaddr:80:80 \
  -p 192.168.33.$ipaddr:3000:3000 \
  -p 192.168.33.$ipaddr:2221:22 \
  -p 192.168.33.$ipaddr:443:443 \
  -p 192.168.33.$ipaddr:100:100 \
  -p 192.168.33.$ipaddr:9000:9000 \
  -v /etc/localtime:/etc/localtime:ro \
  -v $sync_dir:/myapp \
  --name rails \
  -it $image /bin/bash --login

docker exec rails bash -c "cd /myapp/hello_app && rails server"

echo docker attach rails
