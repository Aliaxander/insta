**Current Build Status**

[![Build Status](http://webci.oxgroup.media/buildStatusSvg/12)]
(http://webci.oxgroup.media/buildStatusSvg/12)


# Ox-Framework-install

<br>composer install
<br>
<br>
<br>Create files in root:
<br>
<br>migrations.yml:
<br>name: TestName
<br>migrations_namespace:  TestName
<br>table_name: migration_versions
<br>migrations_directory: migrations
<br>
<br>migrations-db.php:
<br><?php
<br>return array(
<br>    'driver'    => 'pdo_mysql',
<br>    'host'      => 'localhost',
<br>    'user'      => 'root',
<br>    'password'  => '',
<br>    'dbname'    => 'test'
<br>);
<br>
<br>Migration:
<br>php console.php  migrations:generate



Что бы изменить пароль: 1. перейдите по ссылке https://cp.oxcpa.ru/changePassword?email=oxcpa.ru@gmail.com&token=17c75d61196c73ef92c02c2234188d35 2. дваждый введите новый пароль 3. нажмите кнопку "Изменить"
