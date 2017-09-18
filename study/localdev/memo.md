docker run --rm -it localdev_db /bin/bash
docker exec -it localdev_db_1 /bin/bash

docker run --rm -it -p 192.168.33.10:80:80 localdev_web /bin/zsh
docker exec -it localdev_web_1 /bin/zsh
