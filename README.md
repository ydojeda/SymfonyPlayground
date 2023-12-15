# Symfony Playground

A repository for learning PHP OOP concepts and Symfony.

## Goal

✅ To create routes for CRUD of a blogpost.
✅ Integration setup of a Vue project into the SymfonyPlayground (in this case, a simple hello world page)

## Tutorials

[This](https://www.youtube.com/watch?v=pZv93AEJhS8) was partially followed but since the project uses Symfony 4.4, the
project mainly relied on cross-referencing the documentation.

## Dummy Data

Data from [dummyJSON](https://dummyjson.com/docs/posts) was saved in a json file and used to load fixtures into the db

## Future Works

- Make routes RESTful with proper handling of blog user authentication
- Migrate Vue playground components to consume the api in this project

## Running the Project

Install dependencies

```
composer install
yarn install
```

Run the server and load fixtures. The project uses symfony cli and uses docker and mysql (see database
url `symfony var:export --multiline`)

```
symfony server:start
docker-compose up
symfony console doctrine:fixtures:load
```