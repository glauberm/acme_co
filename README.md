# AcmeCo

## Running the project locally

> You need Docker installed and configured on your machine in order to run this project locally.

Build and start the containers:

```
docker compose up
```

> The container user matches UID/GID `1000` by default. If yours differ, build with `UID=$(id -u) GID=$(id -g) docker compose up`.

Install the PHP dependencies:

```
docker exec -it acme_app composer install
```

Set up the environment:

```
docker exec -it acme_app cp .env.example .env
docker exec -it acme_app php artisan key:generate
docker exec -it acme_app php artisan migrate
```

Seed the product catalogue:

```
docker exec -it acme_app php artisan db:seed
```

> The seeder is safe to run more than once. To start from scratch, use `php artisan migrate:fresh --seed`.

Install the frontend dependencies:

```
docker exec -it acme_app npm install
```

Build the frontend assets:

```
docker exec -it acme_app npm run build
```

Visit [localhost:8080](http://localhost:8080/).

## Running the development server

For hot module replacement while working on the React frontend:

```
docker exec -it acme_app npm run dev
```

Keep visiting [localhost:8080](http://localhost:8080/); assets are served by Vite on port `5173`.

## Running tests

```
docker exec -it acme_app php artisan test
```

Tests run against the `database_testing` container, configured in `.env.testing`.

## Databases

| Database    | Host        | Port   | Name        | User   | Password   |
| ----------- | ----------- | ------ | ----------- | ------ | ---------- |
| Development | `localhost` | `3307` | `acme`      | `acme` | `12345678` |
| Testing     | `localhost` | `3308` | `acme_test` | `acme` | `12345678` |

## Assumptions

- **No authentication.** There are no users, logins or sessions to manage. The app behaves as if a single user is always logged in, so there is exactly one basket, stored in the database.
- **Blue Widget costs $7.95.** The assignment lists B01 at $32.95, but the example totals only add up at $7.95 (e.g. B01 + G01 = $7.95 + $24.95 + $4.95 delivery = $37.85). The seeder uses $7.95.
- **Money is handled in integer cents** to avoid floating-point errors.
- **The half-price item is rounded down to the cent** ($32.95 / 2 = $16.47). This is what makes the R01, R01 example total $54.37.
- **Delivery is charged on the subtotal after offers.** For R01, R01 the discounted subtotal is $49.42, which is under $50, so delivery is $4.95.
- **An empty basket costs nothing,** including delivery.
- **The offer applies to every pair.** Three red widgets get one discount, and four get two.
