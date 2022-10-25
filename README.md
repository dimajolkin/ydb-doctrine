## НЕ использовать

Пишется для проверки возможности запуска в маленьком проекте 

```bash
# Для ананимного доступа. Используется при локальной разработке
DATABASE_URL="ydb://localhost:2136/local?discovery=false&iam_config[anonymous]=true&iam_config[insecure]=true"


```
Пример настройки Symfony

```yaml
services:
    ydb-quote-strategy:
        class: Dimajolkin\YdbDoctrine\ORM\QuoteStrategy
doctrine:
    dbal:
        options:
            YBD_URL: '%env(resolve:DATABASE_URL)%'
        driver_class: \Dimajolkin\YdbDoctrine\YdbDriver
        wrapper_class: \Dimajolkin\YdbDoctrine\ConnectWrapper

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
 $table2->addColumn('name', Types::STRING, ['notnull' => false]); // Если колонка не в PR то обязательно not null!
 $table2->setPrimaryKey(['id']);
 $this->connection->createSchemaManager()->createTable($table2);

```
