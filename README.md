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
