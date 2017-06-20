# docker build -t ruby:2.4 ruby/.
# docker run -d -p 80:80 --privileged --name test -it ruby:2.4 /bin/bash
# docker attach test
FROM amazonlinux:2017.03

WORKDIR /root/tmp
RUN rm -rf /etc/localtime && cp /usr/share/zoneinfo/Japan /etc/localtime

RUN yum groupinstall "Development Tools" -y && \
  yum install gcc wget tar sudo git make libxml2 libxml2-devel openssl-devel readline-devel which vim curl -y && \
  curl -O http://ftp.ruby-lang.org/pub/ruby/2.4/ruby-2.4.1.tar.gz && \
    tar -zxvf ruby-2.4.1.tar.gz && \
    cd ruby-2.4.1 && \
    ./configure --disable-install-doc && \
    make && \
    make install && \
    cd .. && \
    rm -r ruby-2.4.1 ruby-2.4.1.tar.gz && \
    echo 'gem: --no-document' > /usr/local/etc/gemrc

# Install Bundler for each version of ruby
RUN \
  echo 'gem: --no-rdoc --no-ri' >> /.gemrc && \
  gem install bundler

# install sqlite3
RUN yum install -y sqlite-devel

# Install Node.js and npm
RUN \
  curl --silent --location https://rpm.nodesource.com/setup_6.x | bash - && \
  yum -y install nodejs

RUN rm -rf /root/tmp

CMD ["/bin/bash"]
