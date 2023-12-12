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
sudo mysqldump [database_name] > [dumpfilename.sql] //for database backup
 sudo mysqldump iomad > iomad_database.sql
mysqldump -u [user name] –p [password] [options] [database_name] [tablename] > [dumpfilename.sql]
mysql -u username -p database_name < file.sql //for upload database

touch index.php // create file 
gedit index.php // edit file

 




