# AcmeCo

## Running the project locally

> You need Docker installed and configured on your machine in order to run this project locally.

Build and start the containers:

```
docker compose up
```

> The container user matches UID/GID `1000` by default. If yours differ, build with `UID=$(id -u) GID=$(id -g) docker compose up`.

Visit [localhost:8080](http://localhost:8080/).

## Databases

| Database    | Host        | Port   | Name        | User   | Password   |
| ----------- | ----------- | ------ | ----------- | ------ | ---------- |
| Development | `localhost` | `3307` | `acme`      | `acme` | `12345678` |
| Testing     | `localhost` | `3308` | `acme_test` | `acme` | `12345678` |
