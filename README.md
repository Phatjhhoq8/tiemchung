# Medicare Vaccine Registration

Laravel 11 application using PHP 8.2 and MySQL 8. Docker is the only required local runtime.

## Development

```bash
cp .env.example .env
docker compose build app
docker compose run --rm app php artisan key:generate
docker compose up -d
docker compose exec app php artisan migrate --seed
```

`db:seed` does not create or restore vaccine records. The vaccine catalog is managed through the admin UI, so reseeding will not undo manual additions, edits, or deactivations.

Open <http://localhost:8000>. MySQL is available on host port `3307`; override it with `FORWARD_DB_PORT=3308 docker compose up -d` when needed.

## Tests

The MySQL container creates both `medicare_codo` and the isolated `yvidlapc_tiemchung_testing` test database.

```bash
docker compose run --rm test
```

Useful commands:

```bash
docker compose logs -f app
docker compose down
docker compose down -v   # Also delete development and test data.
```
