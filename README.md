<p align="center">
<img src="https://www.locod.io/locodio_square.svg" width="100">
</p>
<p align="center">
<a href="LICENSE" target="_blank">
        <img src="https://img.shields.io/github/license/locod-io/locod.io" alt="GitHub license">
    </a>
</p>

[locod.io](https://www.locod.io) is a free and open-source web application 
for data-modeling and code generation.

Start with modeling your data. Use this data-model as a quick start for 
your data-driven applications. With its template based approach, locod.io can 
generate code for any kind of languages. Supporting your own style and technologies.

[It is licensed under the MIT License](LICENSE)

## Technical requirements

* A webserver: Apache, NGINX or the build-in Symfony Dev Server
* A database system that is supported by the Doctrine ORM library: MySQL, PostgreSQL,...
  * For developing this project MySQL 5.7 is used (same as the production database)
* PHP 8.1 or higher and these PHP extensions (which are installed and enabled by
  default in most PHP 8 installations): Ctype, iconv, PCRE, Session, SimpleXML, and Tokenizer.
* [Composer](https://getcomposer.org/), which is used to install PHP packages
* [NodeJS](https://nodejs.org/) for npm and building the Vue.js app UI & docs (VuePress)
* [Yarn](https://yarnpkg.com/), javascript package manager (for Symfony Encore)

## Project Setup

This application is build upon the [Symfony](https://symfony.com/) framework.
Extra information in setting up an existing Symfony project can be
found [here](https://symfony.com/doc/current/setup.html#setting-up-an-existing-symfony-project).

Make sure the `var` directory in the project is writable.

### 1. Make an .env file

Copy the `.env.example` file to `.env`

* APP_ENV = application environment (dev or prod)
* DATABASE_URL = database type and credentials for Doctrine
* MAILER_DSN = the url of the smtp service for sending emails

The settings in the `.env.example` are ready to go for use with Docker. 

### 2. Running the application via Docker (optional, for development)

If you have Docker installed on your computer, you can use that
for running the application stack locally.

There is a `docker-compose.yml` available in the root of the project.
You can fire up this configuration with this command:

```sh
docker-compose up -d
```
When all the images are downloaded and the services are started, 
you can access the application on `http://localhost:8080`

Following images are used:
  * `mysql:5.7`: database instance running on port 3306
  * `nginx:alpine`: linux webserver running on port 8080
  * `wodby/php:8.1`: php environment
  * `mailhog/mailhog`: a mail server that intercepts all outgoing mails

The mailhog interface is available on `http://localhost:8025`

If those ports are in use on your computer you always can change those
in the docker configuration file.

### 3. Install the PHP Libraries

If you are running the application with Docker, first open a terminal in the php container, 
and then install the PHP libraries, described in the composer.json config file.

```sh
docker exec -it <php-container-name> /bin/bash

composer install
```
If you are running the application without Docker, but have Composer installed just run:

```sh
composer install
```
### 4. Create & setup the database

With following commands you can create an empty database with the table structure setup.
If you are using docker, make sure you're in the container terminal (see above).

```sh
php bin/console doctrine:database:create
php bin/console doctrine:schema:update --force
```
### 5. Install all the frontend (js,css) dependencies & build the parts of the UI

#### 5.1 Symfony Encore 
Install the javascript libraries
```sh
yarn install
```
Build the production files
```sh
yarn encore prod
```

#### 5.2 Locod.io app 
Install the javascript libraries
```sh
yarn locodio-app-install
```
Build the production files
```sh
yarn locodio-app-build
```
#### 5.3 Locod.io docs
Install the javascript libraries
```sh
yarn locodio-docs-install
```
Build the production files
```sh
yarn locodio-docs-build
yarn locodio-docs-publish (linux only)
```

## Development (optional)

There is some basic data that you can load into your database for the first time.
To get started with your development. You can view and edit them in the 
`src/DataFixtures/UserFixtures.php` file.

To load the fixtures in the database, run following command.
If you are using docker, make sure you're in the container terminal (see above).
```sh
php bin/console doctrine:fixtures:load
```

## Structure of the code

### `/assets/`

All assets that [Symfony Encore](https://symfony.com/doc/current/frontend/encore/installation.html#installing-encore-in-symfony-applications)
is using are located in the assets folder.
The `webpack.config.js` in the root of the project contains all the
entries for the different javascript entries and stylesheets used in the templates.

* `/`  javascript entries.
* `styles` stylesheets 
* `components` some VueJS components that are part of site  

Compiling and building these files is necessary to copy them into the public folder.
```sh
yarn encore prod
```
---

### `/frontends/`

The frontends folder contains two `vite` based Vue 3 frontends.

`locodio_app` is Single Page Application(SPA) made with the 
[Vue 3](https://vuejs.org/) framework. Development and building is 
done with [ViteJS](https://vitejs.dev/). 
For more details [see here](frontends/locodio_app/README.md).

`locodio_docs` is [VuePress](https://v2.vuepress.vuejs.org/) based project.
Development and building is done with [ViteJS](https://vitejs.dev/). 
For more details [see here](frontends/locodio_docs/README.md).

Both user interface applications become available in the public folder when you
_'build'_ them.

---
### `/config/`

Contains all the Symfony configurations for the application.

---
### `/src/`

In typical Symfony projects all the application code is placed here,
but in this project all application related code is moved to the `application` folder.
Except for the migrations and dataFixtures.

There is as small change in the `composer.json` to make that work.
```json
"autoload": {
    "psr-4": {
        "App\\": "src/",
        "App\\Locodio\\": "application/Locodio/"
    }
},
```
---
### `/application/`

`/application/Locodio` is the main folder where the whole application lives.
The folder structure in structured in three layers:

* `Application` (query & command)
* `Domain` (entities, domain model)
* `Infrastructure` (database, translations, templates,...)

[An introduction to Domain-Driven Design](https://medium.com/inato/an-introduction-to-domain-driven-design-386754392465)

---
### `/public/`

This is the public folder of the web-application. 
It contains the main `index.php` file, some static `assets`, 
and all the frontends that are build and compiled from its 
source code, elsewhere located in this project.

After building those you should have those four folders in the public directory:
* `build/` build files from the assets folder (Symfony Encore)
* `bundles/` build files from Symfony bundles
* `docs/`  build files from the `frontends/locodio_docs` folder
* `locodio_app/` build files from the `frontends/locodio_app` folder
