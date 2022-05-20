# News

## How to configure dev environment

- Install and run Docker:
    1. Run `docker network create --driver=bridge dev`
- Install and configure mysql:
    1. Run mysql in network `dev`
- Configure connect to db in .env
- Run `build.sh` and then `run.sh` in project folder
- Run `docker exec -it news-ratio.dock bash`
    ```bash
    composer i
    bin/console d:d:c - creating db
    bin/console d:m:m - migration
    bin/console d:f:l - load fixtures
