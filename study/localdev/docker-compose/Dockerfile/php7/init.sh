#!/bin/sh

service httpd start

cat <<EOF >>~/.bashrc
trap 'service httpd stop; exit 0' TERM
EOF
exec /bin/bash
