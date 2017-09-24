# imageの作成
docker-compose build

# コンテナ起動
docker-compose up -d

# コンテナ削除
docker-compose kill
docker-compose rm

# コンテナに入る
docker exec -it localdev_web_1 /bin/zsh
docker exec -it localdev_db_1 /bin/zsh
抜ける時はcontrol+p,control+q


# laravelのプロジェクト作成
cd /var/www/laravel
composer create-project --prefer-dist laravel/laravel blog "5.4.*"


# dockerfile確認用
docker run --rm -it localdev_db /bin/zsh


docker run --rm -it -p 192.168.33.10:80:80 localdev_web /bin/zsh
docker exec -it localdev_web_1 /bin/zsh
