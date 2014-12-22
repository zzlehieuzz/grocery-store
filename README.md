########################################################
SETUP:

1. Enviroment:
- PHP 5.4.x (a minimum version)
- Apache 2.2 
- MySQL 5.6
(XAMPP 1.8.*)
2. Checkout from: https://github.com/zzlehieuzz/grocery-store.git
3. Install composer: "composer install" (MAC: "php composer.phar install")
- Check install: php composer (MAC: php composer.phar)
- Download composer: "curl -sS https://getcomposer.org/installer | php"
4. Create 3 databases: sof_log, sof_master, sof_slave (collation: utf8_genaral_ci)
5. In file: app/config/parameters.yml, configure with your MySQL account and port. 
6. Generate sql to database
php app/console doctrine:schema:create --em=log
php app/console doctrine:schema:create --em=default (Now, don't need it)
php app/console doctrine:schema:create --dump-sql > dmm.sql
7. Setting visual host in \xampp\apache\conf\extra\httpd-vhosts.conf
  Alias /sofapi "path/to/project/"
  <Directory "path/to/project">
    Options Indexes FollowSymLinks MultiViews
    AllowOverride none
    Require all granted
  </Directory>
8. Test with URL: yourdomain/web/app_dev.php/default/index/name
  C:\ProgramData\ComposerSetup\bin\composer.phar self-update
#######################################################
HELP:

  Update project:    php composer self-update
                     php composer update
  Update db:         php app/console doctrine:schema:update --em=default --force
                     php -d memory_limit=200M app/console doctrine:schema:update --dump-sql
  Load data default: php app/console khepin:yamlfixtures:load dev
  Clear cache:       php app/console cache:clear
                     php app/console cache:clear --env=prod
                     php app/console cache:clear --env=dev
         
########################################################
  SQL REF:
  
  http://docs.doctrine-project.org/en/latest/index.html
  http://docs.doctrine-project.org/en/latest/reference/query-builder.html
  http://docs.doctrine-project.org/en/latest/reference/dql-doctrine-query-language.html
  Select object with specified fields: Normally, when select object, it will be include all fields in table (select * from..),
  so that if select object with specified fields (select user.name, user.pass... from...)
      "partial u.{id, name, pass} AS user from ..."
  Select array data, not array object: ->getQuery()->getArrayResult()
  Select array object: ->getQuery()->getResult()
  
  Call function in repository:
  $this->get('entity_service')->process('EntityName:functionName', args,...);
########################################################

Cpanel Username:     b15_15455114
Cpanel Password:     ngusaocho10489
Your URL:            http://grocery-store.byethost15.com or http://www.grocery-store.byethost15.com
FTP Server :         ftp.byethost15.com
FTP Login :          b15_15455114
FTP Password :       ngusaocho10489
MySQL Database Name: sql204.byethost15.com
MySQL Username :     b15_15455114
MySQL Password :     ngusaocho10489
MySQL Server:        SEE THE CPANEL
b15_15455114_grocery_store

########################################################

Cpanel Username:    adorzhang    
Cpanel Password:    0937669567   
Your URL:  http://adorzhang.com               
FTP Server :   ftp.adorzhang.com       
FTP Login :     test@adorzhang.com        
FTP Password :  test123456        
MySQL Database Name: adorzhang_store
MySQL Username : adorzhang_store
MySQL Password : test123456       
MySQL Server: localhost          

Cpanel URL: http://103.18.5.67:2222/

chincchi.com
ftp: 125.253.124.100
http://125.253.124.100:2082/login.php3
username : chincchi
password: thanhdp123456

db:
chincchi_store
store_123

ALTER TABLE invoice ADD delivery_Status INT NOT NULL COMMENT '10:delivery_Status';