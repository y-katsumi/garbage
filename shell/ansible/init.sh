# docker run -d -p 192.168.33.10:2220:22 --privileged --name test  -it amazonlinux:2017.03 /bin/bash
yum update -y
yum install -y openssh-server passwd expect ansible wget git gcc bzip2-devel sqlite-devel git patch gcc openssl-devel python-devel libxslt-devel libffi-devel vim

expect -c "
set timeout 5
spawn passwd
expect \"password:\"
send \"newpassword\n\"
expect \"password:\"
send \"newpassword\n\"
expect \"passwd: all authentication tokens updated successfully.\"
send \"exit\n\"
"
sed -i -e 's/PasswordAuthentication no/PasswordAuthentication yes/g' /etc/ssh/sshd_config
sed -i -e 's/PermitRootLogin forced-commands-only/PermitRootLogin yes/g' /etc/ssh/sshd_config


git clone https://github.com/tagomoris/xbuild.git
xbuild/python-install 2.7.10 /opt/python-2.7
# /opt/python-2.7/bin/pip install --upgrade pip
/opt/python-2.7/bin/pip install ansible
echo 'PATH=/opt/python-2.7/bin:$PATH' >> ~/.bashrc
source ~/.bashrc

service sshd restart

# docker commit a9f53838f3ec ansible

# docker run -d --privileged --name test  -it ubuntu:16.04 /bin/bash
