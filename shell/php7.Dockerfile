# docker build -t php7 php7/.
# docker run -d -p 80:80 --privileged --name test -it amazonlinux:2017.03 /bin/bash
# docker run -d -p 80:80 --privileged --name test  -it php7 /bin/bash
# docker exec -it test /bin/bash
FROM amazonlinux:2017.03

RUN rm -rf /etc/localtime && \
    cp /usr/share/zoneinfo/Japan /etc/localtime && \
    yum update -y && \
    yum install gcc wget tar sudo git make libxml2 -y && \
    yum -y install libxml2-devel

# 参考
# http://qiita.com/inakadegaebal/items/b57cf10339978d638305
# http://qiita.com/yunano/items/9637ee21a71eba197345
WORKDIR /root/init
# RUN yum install -y epel-release
# RUN wget http://dl.fedoraproject.org/pub/epel/7/x86_64/e/epel-release-7-9.noarch.rpm && \
#     rpm -ivh epel-release-7-9.noarch.rpm && \
#     rpm -ivh http://rpms.famillecollet.com/enterprise/remi-release-7.rpm
# phpがうまくはいらなかった。。
RUN yum install httpd24 mod24_ssl.x86_64 -y
# RUN yum install httpd mod_ssl -y
RUN sed -i -e '/^<Directory \"\/var\/www\/html\">/a EnableMMAP Off' /etc/httpd/conf/httpd.conf && \
    sed -i -e '/^<Directory \"\/var\/www\/html\">/a EnableSendfile Off' /etc/httpd/conf/httpd.conf && \
    sed -i -e '/^<Directory \"\/var\/www\/html\">/a Options Indexes FollowSymLinks' /etc/httpd/conf/httpd.conf && \
    sed -i -e '/^<Directory \"\/var\/www\/html\">/a Require all granted' /etc/httpd/conf/httpd.conf && \
    sed -i -e 's/AllowOverride None/AllowOverride All/g' /etc/httpd/conf/httpd.conf

RUN yum -y install http://dev.mysql.com/get/mysql57-community-release-el6-7.noarch.rpm && \
    yum -y install mysql-community-server

# http://qiita.com/mats116/items/59615a169a45884c2548
RUN cp /var/log/mysqld.log /var/log/mysqld.log_pass
RUN echo "[mysqld]"  >> /etc/my.cnf && \
    echo "innodb_file_per_table=1"  >> /etc/my.cnf && \
    echo "character-set-server=utf8"  >> /etc/my.cnf && \
    echo "skip-character-set-client-handshake"  >> /etc/my.cnf && \
    echo "default-storage-engine=INNODB"  >> /etc/my.cnf && \
    echo "[mysqldump]"  >> /etc/my.cnf && \
    echo "default-character-set=utf8"  >> /etc/my.cnf && \
    echo "[mysql]"  >> /etc/my.cnf && \
    echo "default-character-set=utf8"  >> /etc/my.cnf

RUN yum install php70 php70-cli php70-zip.x86_64 -y && \
    yum install php70-common.x86_64  php70-pdo.x86_64 php70-mysqlnd.x86_64 -y && \
    yum install php70-devel.x86_64 php70-gd.x86_64 php70-mcrypt.x86_64 php70-mbstring.x86_64 php70-xml.x86_64 -y

RUN sed -i -e 's/;date.timezone =/date.timezone = Asia\/Tokyo/g' /etc/php.ini

RUN curl -sS https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer

RUN composer config -g repos.packagist composer https://packagist.jp && \
    composer global require "hirak/prestissimo:^0.3"
ENV PATH $PATH:/root/.composer/vendor/bin

# http://qiita.com/mats116/items/59615a169a45884c2548
RUN echo "NETWORKING=yes" >/etc/sysconfig/network

CMD ["/bin/bash"]

# 入るときはdocker attach でなく
# docker exec -it {name} /bin/bash
# をつかう
