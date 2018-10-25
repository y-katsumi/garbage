## 起動
docker-compose up -d

## コンテナ内に入る
docker exec -it docker_web_1 /bin/bash

## adminerのパス
http://localhost:81/?server=docker_db_1&username=root
