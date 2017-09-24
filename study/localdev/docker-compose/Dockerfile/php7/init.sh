#!/bin/sh

rm -rf /var/www/html
ln -s ${DOCUMENT_ROOT} /var/www/html

service httpd start

cat <<EOF >>~/.bashrc
trap 'service httpd stop; exit 0' TERM
EOF
exec /bin/zsh
