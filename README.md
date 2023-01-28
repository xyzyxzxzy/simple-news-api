# News

## How to configure dev environment

- Run `docker compose up`
- Create `.env.local` file and set `DATABASE_URL` variable
- Run `docker exec -it news.dock bash`
    ```bash
    composer i
    bin/console lexik:jwt:generate-keypair - generate the SSL keys
    bin/console d:m:m - migration
    bin/console d:f:l - load fixtures

## Routes

- Auth:
    1. `/login`: `POST` - login
        ```bash
        {"username":"admin", "password":"admin"}
    2. `/token/refresh`: `POST` - refresh token
        ```bash
        {"refresh_token": "refresh_token"}

- Upload(auth):
    1. `/admin/upload/`: `POST` - upload files(Flow.js)

- News:
    1. `/news/`: `GET` - list news
        + `pg: integer` - page
        + `on: integer` - amount of news
        + `dateFilter: string` - filtering by news publication date (format: d-m-Y)
        + `tagIds: array[int]` - filtering by news tags

         ```bash
         {"pg": 1, "on": 5, "dateFilter": "05-01-2022", "tagsIds": [1, 2, 3]}
        ```
    2. `/news/{id}`: `GET` - item news
    3. `/news/likes/{id}`: `GET` - news like list
- News(auth):
    1. `/admin/news/`: `POST` - create news
    2. `/admin/news/{id}`: `PATCH` - update news
    3. `/admin/news/{id}`: `DELETE` - delete news
    4. `/news/like/{id}`: `POST` - like the news
    5. `/news/like/{id}`: `DELETE` - dislike the news

- Tags
    1. `/tag/`: `GET` - list tags
        + `pg: integer` - page
        + `on: integer` - amount of tags

         ```bash
         {"pg": 1, "on": 5}

        ```
    2. `/tag/{id}`: `GET` - item tag

- Tags(auth):
    1. `/admin/tag/`: `POST` - create tag
    2. `/admin/tag/{id}`: `PATCH` - update tag
    3. `/admin/tag/{id}`: `DELETE` - delete tag