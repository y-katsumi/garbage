echo 192.168.33.11 > hosts
ansible -i hosts 192.168.33.11 --ask-pass -c paramiko -m ping
rkr4izdi2


ansible-playbook -i hosts playbook.yml --syntax-check
ansible-playbook -i hosts playbook.yml --ask-pass -c paramiko --check
ansible-playbook -i hosts playbook.yml --ask-pass -c paramiko
rkr4izdi2
