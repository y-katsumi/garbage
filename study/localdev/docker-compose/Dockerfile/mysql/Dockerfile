# docker build -t php7 php7/.
# docker run -d -p 80:80 --privileged --name test -it amazonlinux:2017.03 /bin/bash
# docker run -d -p 80:80 --privileged --name test  -it php7 /bin/bash
# docker exec -it test /bin/bash
FROM amazonlinux:2017.03

RUN rm -rf /etc/localtime && \
    cp /usr/share/zoneinfo/Japan /etc/localtime && \
    yum update -y && \
    yum install gcc wget tar sudo git make libxml2 -y && \
    yum -y install libxml2-devel zsh

RUN yum -y install http://dev.mysql.com/get/mysql57-community-release-el6-7.noarch.rpm && \
    yum -y install mysql-community-server

# http://qiita.com/mats116/items/59615a169a45884c2548
RUN cp /var/log/mysqld.log /var/log/mysqld.first_log
RUN echo "[mysqld]"  >> /etc/my.cnf && \
    echo "explicit_defaults_for_timestamp = true"  >> /etc/my.cnf && \
    echo "skip-grant-tables"  >> /etc/my.cnf && \
    echo "innodb_file_per_table=1"  >> /etc/my.cnf && \
    echo "character-set-server=utf8"  >> /etc/my.cnf && \
    echo "skip-character-set-client-handshake"  >> /etc/my.cnf && \
    echo "default-storage-engine=INNODB"  >> /etc/my.cnf && \
    echo "[mysqldump]"  >> /etc/my.cnf && \
    echo "default-character-set=utf8"  >> /etc/my.cnf && \
    echo "[mysql]"  >> /etc/my.cnf && \
    echo "default-character-set=utf8"  >> /etc/my.cnf

# http://qiita.com/mats116/items/59615a169a45884c2548
RUN echo "NETWORKING=yes" >/etc/sysconfig/network

ENV LANG ja_JP.UTF-8

RUN echo "compinit" >> ~/.zshrc && \
    echo "autoload -U compinit; compinit" >> ~/.zshrc && \
    echo "PROMPT=\"docker %m $ \"" >> ~/.zshrc

EXPOSE 3306

ADD init.sh /usr/local/bin/init.sh
RUN chmod u+x /usr/local/bin/init.sh
CMD ["/usr/local/bin/init.sh"]

# 入るときはdocker attach でなく
# docker exec -it {name} /bin/bash
# をつかう
