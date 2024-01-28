# YASBS - Yet Another Secure Book Shop

To run the application, first create a `.env` file, then

```
docker compose build
docker compose up
```


All data in MySQL is persisted in the `db_data` Docker volume, to remove all containers and volumes:
```
docker compose down -v
```