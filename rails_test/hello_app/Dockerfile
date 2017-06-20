# http://qiita.com/k-shogo/items/592e243f9763d0c3b10a
# docker build -t rails:5.1 .
# docker run -d -p 192.168.33.10:3000:3000 -v /etc/localtime:/etc/localtime:ro -v /vagrant/src/rails_test:/vagrant/src/rails_test --name test -it ruby:2.4 /bin/bash --login
# docker run -d -p 192.168.33.10:3000:3000 -v /etc/localtime:/etc/localtime:ro -v /vagrant/src/rails_test:/myapp --name test -it rails:5.1 /bin/bash --login
# docker attach test
FROM ruby:2.4

RUN mkdir /myapp
WORKDIR /myapp

ADD Gemfile Gemfile
ADD Gemfile.lock Gemfile.lock
RUN bundle config without test development doc
RUN bundle install
# rails _5.1.1_ new hello_app
ADD . /myapp

ENV RAILS_ENV development
# RUN bundle exec rake db:create
# RUN bundle exec rake db:migrate
# RUN bundle exec rake assets:precompile

# Add configuration files in repository to filesystem
# ADD dotenv          /myapp/.env
# ADD start-server.sh /usr/bin/start-server
# RUN chmod +x /usr/bin/start-server

CMD ["/bin/bash"]
