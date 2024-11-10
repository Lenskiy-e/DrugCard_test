# Run project

## Without Taskfile
```console
cp .env.example .env
fill .env
docker-compose up -d
docker exec -ti app bash
composer install
php bin/console doctrine:migrations:migrate --no-interaction
```

## With Taskfile
```console
cp .env.example .env
fill .env
task start
```
## To parse and fill products

```console
docker exec -ti app bash
php bin/console app:run
php bin/console messenger:consume async
```