### What's included
- PHP 7.3
- Nginx 1.15
- MySQL 5.7
- Symfony 4.1
- Elasticsearch 7.9.3
- Kibana 7.9.3
- Composer 1.10.10

### Docker (local)
- Create `.env` file and configure this file
    ```
    $ cp .env.docker .env
    ```
- Automatic start
    ```
    $ chmod +x /start-local-docker-containers.sh && ./start-local-docker-containers.sh
    ```
- Manual start
    ```
    $ docker-compose up -d
    $ docker-compose exec php composer install
    ```
- Add hosts
    ```
    ...
    172.80.0.6      symfony.localhost www.symfony.localhost
    ...
    ```