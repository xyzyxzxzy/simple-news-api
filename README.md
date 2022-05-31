# News

## How to configure dev environment

- Install and run Docker:
    1. Run `docker network create --driver=bridge dev`
- Install and configure mysql:
    1. Run mysql in network `dev`
- Create `.env.local` file and configure connect to db (example can be obtained from `.env` file)
- Run `build.sh` and then `run.sh` in project folder
- Run `docker exec -it news.dock bash`
    ```bash
    composer i
    bin/console lexik:jwt:generate-keypair - generate the SSL keys
    bin/console d:d:c - creating db
    bin/console d:m:m - migration
    bin/console d:f:l - load fixtures
