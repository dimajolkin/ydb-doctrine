## НЕ использовать


```bash
    composer require dimajolkin/ydb-doctrine:dev-master
```

Пишется для проверки возможности запуска в маленьком проекте 

```bash
# Для ананимного доступа. Используется при локальной разработке
DATABASE_URL="ydb://localhost:2136/local?discovery=false&iam_config[anonymous]=true&iam_config[insecure]=true"

# 
DATABASE_URL="ydb://ydb.serverless.yandexcloud.net:2135/ru-central1/xxxxxxx/xxxxxxx?discovery=false&iam_config[temp_dir]=/tmp&iam_config[use_metadata]=true"

```
Пример настройки Symfony

```yaml
parameters:
  doctrine.orm.entity_manager.class: Dimajolkin\YdbDoctrine\ORM\EntityManager

#services:
#  doctrine.dbal.logging_middleware:
#    class: 'Dimajolkin\YdbDoctrine\DBAL\Driver\Middleware\LoggerMiddleware'

doctrine:
    dbal:
        options:
            YBD_URL: '%env(resolve:DATABASE_URL)%'
        driver_class: Dimajolkin\YdbDoctrine\Driver\YdbDriver
        wrapper_class: Dimajolkin\YdbDoctrine\YdbConnection
        server_version: 1.4
    dql:
      string_functions:
        rand: Dimajolkin\YdbDoctrine\ORM\Functions\Rand
```


Генерация таблиц

```php
 use Doctrine\DBAL\Schema\Table;

 $table1 = new Table('event_bonuses');
 $table1->addColumn('event_id', Types::STRING);
 $table1->addColumn('event_bonuses_id', Types::STRING);
 $table1->setPrimaryKey(['event_id', 'event_bonuses_id']);
 $this->connection->createSchemaManager()->createTable($table1);

 $table2 = new Table('event');
 $table2->addColumn('id', Types::STRING);
 $table2->addColumn('name', Types::STRING, ['notnull' => false]); // Если колонка не в PK то обязательно not null!
 $table2->setPrimaryKey(['id']);
 $this->connection->createSchemaManager()->createTable($table2);

```

 Функции

1. RAND(columnName) - 
