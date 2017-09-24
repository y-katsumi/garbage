#!/bin/sh

sudo rm -rf /var/lib/mysql/mysql.sock.lock
service mysqld start

cat <<EOF >>~/.zshrc
trap 'service mysqld stop; exit 0' TERM
EOF
exec /bin/zsh
