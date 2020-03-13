# api_webservice

## LAMP docker

```yaml
version: '2'

services:
    web:
        depends_on:
            - db
        image: lavoweb/php-7.3
        ports:
            - "8081:80"
        volumes:
            - ./www:/var/www/html
        links:
            - db:db

    db:
        image: mysql:5.5
        volumes:
            - ./mysql:/var/lib/mysql
        ports:
            - "3306:3306"
        environment:
            MYSQL_DATABASE: php_api
            MYSQL_USER: user
            MYSQL_PASSWORD: user
            MYSQL_ROOT_PASSWORD: root

    myadmin:
        depends_on:
            - db
        image: phpmyadmin/phpmyadmin
        environment:
            PMA_HOST: db
            MYSQL_ROOT_PASSWORD: root
        ports:
            - "8080:80"
        links:
            - db:db
```