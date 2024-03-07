<?php
ssh -i Magento.pem ubuntu@ec2-35-75-163-250.ap-northeast-1.compute.amazonaws.com
ssh [username]@[domain_or_IP]
ssh [pemfile][username]@[domain_or_IP] |||||||  ssh -i vikashIOMAD.pem ubuntu@65.1.148.95

cd /var/www/html
sudo php bin/magento cache:clean
sudo php bin/magento cache:flush
sudo php bin/magento setup:di:compile
sudo -r [zipname.zip] [filename] //Zip file
unzip [filename] //unzip file
sudo chmod -R 777 [filename.ext]//permission
sudo mysql // for sql
// than open directory where you take backup
sudo mysqldump -u [mysql_user] -p [database_name] > [dumpfilename.sql] //for database backup
mysqldump -u [user_name] –p [password] [options] [database_name] [tablename] > [dumpfilename.sql]
mysqldump -u phpmyadmin -p theteflacademy > theteflacademy20240112.sql

mysql -u username -p database_name < file.sql //for upload database
mysql -u phpmyadmin -p moodleDB401 < theteflacademy20240112.sql

touch index.php // create file 
gedit index.php // edit file

 

mysqldump -u root -p wete2015_lms > wete2015_lms2024.sql





ssh -i fivestudents-ec2.pem ubuntu@ec2-18-156-154-44.eu-central-1.compute.amazonaws.com


http://www.workplace-english-training.com/emagazine/administrator/
xipat
xipat100

FTP login (Readyspace)
host: https://www.workplace-english-training.com
Protocol: FTP - file transfer protocol
Port: Leave blank or use 21
Encryption: Use explicit FTP over TSL if available
Login type: Normal
User: wete2015
Password: NATnTx8LhT7P4pCm

ssh wete2015@www.workplace-english-training.com