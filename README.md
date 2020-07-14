# BlablaMovieAPI

This repository is the back-end part of : https://github.com/Mow69/BlablamovieApplication.

Installation
------------

* Install the dependencies with Composer :
  ~~~bash
  composer install
  ~~~
  
* Connect to db with the environment variables :
Create a file .env.local
Add the next variables to the .env.local :
  ~~~
  DATABASE_URL
  ~~~

* Create db and tables:
  ~~~bash
  php app/console doctrine:migrations:migrate
  ~~~
  
* Create users in db:
  ~~~bash
  php bin/console doctrine:fixtures:load
  ~~~

* Run application:
    ~~~bash
    php app/console server:run
    ~~~
