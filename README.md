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
  Load data default: php app/console khepin:yamlfixtures:load dev
  Clear cache:       php app/console cache:clear

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

########################################################

link test: http://adorzhang.com/exp-grocery-store/web/Login
link test: http://adorzhang.com/exp-grocery-store/web/Common_Index

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

generateEntity:
1. Create Folder db-scv in root dir example -> sof/db-csv
2. Empty db-csv folder if have anything exist.
2. Create file csv, put in db-csv
exp:
filename: goods_name_information.txt
Content:
商品名称情報(goods_name_information)
1	goods_id	商品ID	PK	○	INT
2	goods_name	商品名		○	VARCHAR			商品の名称
3	image_information	画像情報		○	VARCHAR			商品のアイコン
4	subtitle	小見出し		○	VARCHAR			商品の説明
5	goods_explanation	商品説明		○	VARCHAR			セットの販売商品としての説明文、ショップで表示される


3. In web browser run app_dev.php/generateEntity
4. In terminal: run with every file: php app/console doctrine:generate:entities SofApiBundle:EntityName --no-backup
exp:
php app/console doctrine:generate:entities SofApiBundle:GoodsNameInformation --no-backup
php app/console doctrine:generate:entities SofApiBundle:EncounterMaster --no-backup
5. Change extend Class in repository: EntityRepository -> BaseRepository
exp:
class InformationRepository extends EntityRepository
-> class GoodsNameInformationRepository extends BaseRepository
delete row: use Doctrine\ORM\EntityRepository;
6. Update DB:
php app/console doctrine:schema:update --em=default --force
7. Commit to svn (Entity, Repository, Const)