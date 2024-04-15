# PMS-API
## Table of Contents

- [Description](#description)
  - [Strategy](#strategy)
    - [Structure design](#design-and-principles)
  - [Features](#features)
  - [Improvements](#possible-improvements)
- [Infrastructure](#infrastructure-used)
  - [Symfony Packages](#installed-symfony-packages)
- [Getting Started](#getting-started)
  - [Run using composer](#run-using-composer)
  - [Run using docker](#run-using-docker)
    - [Next steps](#important)
- [How it works?](#how-it-works)
  - [API](#api)
  - [PHPUnit Testing](#phpunit-testing)
  - [xDebug](#xdebug-debugger)
  - [Docker client host](#__client_host__-)
- [Troubleshooting](#troubleshooting)

## Description
Use of a standardized booking format that is independent of the source format (PMS). To achieve this, it is necessary to design and implement an **endpoint in an
HTTP REST API** that allows querying for a **hotel ID** in DESTINATION **and room number** to check if there is an
active reservation and retrieve its data in the following format:
``` 
{
  "hotel_id": "49001",
  "hotel_name": "Hotel con ID Externo 49001",
  "guest": {
    "name": "John",
    "lastname": "Doe",
    "birthdate": "1900-01-01",
    "passport": "13charspassport",
    "country": "ES"
  },
  "booking": {
    "locator": "...locator...",
    "room": "291",
    "check_in": "2022-01-31",
    "check_out": "2022-02-08",
    "pax": {
      "adults": 1,
      "kids": 0,
      "babies": 0
    }
  },
  "created": "2022-01-31 17:39:38",
  "signature": "e8b558125c709621bd5a80ca25f772cc7a3a4b8b0b86478f355740af5d7558a8"
}
```
### Strategy
The controller calls a service that performs **ETL strategies** to extract the data, transform it, and load it into the target system.

All the operations are performed **synchronously** (Real-time Processing), requesting the source data, transforming it, and returning it to the target structure.

The use of an "incremental" value (timestamp) ensures that only the **new changes made** to the source since the last run are captured and processed, reducing the volume of data transferred and improving efficiency **(Change Data Capture strategy)**

For this purpose, the service follows this order :
 - Fetch data source
 - Decode response
 - Transform to target Entities
 - Persist only when there's new content
 - Return data that was requested

#### Design and Principles
The project structure follows the **hexagonal architecture** of Application, Domain, and Infrastructure.

Design patterns used:
- Dependency Injection
- Strategy (*PMStransformer*)
- Repository (*BookingRepository*)
- Factory (*ResponseHandler*)

Design principles used:
- Single Responsibility principle (SRP)
- Dependency Inversion (DI)
- DRY
- Serialize & Deserialize plus Normalize

### Features
The following key features are implemented
#### System
* Redis. A cache system to manage the `timestamp` parameter and temporary storage of the requested booking by the user.

#### Project
* The project is prepared to manage that a Booking can have (1:N) multiple Guest, because of the ArrayCollection implementation, despite the source not including an object for now, and only returning one item (guest).
* Property validation (mostly the type) for the Entities before persisting them, including:
  * Restrict `hotelId` value to only have the mapped ones (Choice).
  * Verify the source hash `signature` for **security**, and discard the data if it's not valid.
  * Guest `country` value to mandatory follow the  [country alpha-2 ISO.](https://en.wikipedia.org/wiki/ISO_3166-1_alpha-2)
  * Timestamps. Automated creation of `created` and `updated` table columns.
  
#### Good practices
* Manual logging and generic Exceptions catching during the process.
* Mandatory parameters `hotel` and `room` for the endpoint call.
* Having both `id` and `uuid` fields allows easier data manipulation in the database and for frontend reference between objects.
  
#### Logic
* Transform source data to DTO, and then denormalize it with a serializer.
* Persist data only if doesn't exist.
* Normalize the stored data to JSON response with a serializer.
* Adapters. Change of stack easily by only adapting the Infrastructure layer.
  
#### Performance
* From scratch, the process took 15-16 seconds to fetch source data, store it, and return the requested one.
* Later, an average of 400 to 500 milliseconds to return an already stored booking. This value jumped up to 1.2 seconds max when my computer was busy.

### Possible improvements
* Some required composer packages have version conflicts with the newest Symfony releases. I had to downgrade from SF 7 to 6.4 because of it, plus downgrade the ORM bundle below version 3. Maybe substitute these packages for others.
* It would be interesting to keep stored somewhere the original data source booking from PMS, maybe in another NoSQL database like Elastic.
* The timestamp column `created` is not used for its original purpose, which would be the created *datetime* of the row record. Instead, the `created` from the data source is being stored in that field.
* Some logic from the service, like the one for the data storage, could be transferred to the infrastructure layer in an adapter, it wasn't done to not add extra complexity.
* An output **DTO** should be used to return data results in the **CustomBookingNormalizer**, to standardize the desired output against any client.
* Some **constants** defined should be retrieved from the cache system, database, or the `.env`, so they would be easier to set on demand. For example, the mapper of source `hotel_id` to desired format destination.
* It would be interesting to add the [Json-Schema](https://github.com/justinrainbow/json-schema) validation for the expected source data to have a 1st layer filter instead of the ones that are checked on the code.

***

## Infrastructure used
* Symfony 6.4
* Docker
    * PHP 8.2 (w/ opcache & [xDebug](#xdebug-debugger))
    * Nginx
    * MariaDB 11.1.4
    * Redis 7.2.4
    * Adminer 

### Installed Symfony Packages
* **phpunit/phpunit**: testing framework for PHP
* **doctrine/orm**: simplifies database interactions by mapping database tables to PHP objects.
* **doctrine/doctrine-fixtures-bundle**: predefined sets of data used for testing or populating a database with initial data.
* **symfony/http-client**: HTTP client for making HTTP requests and interacting with web services.
* **symfony/validator**: tools for validating data according to predefined rules.
* **symfony/maker-bundle**: facilitates rapid development by automating the creation of boilerplate code.
* **snc/redis-bundle**: integration with Redis.
* **predis/predis**: integration with Redis.
* **snc/redis-bundle**: implementation of the Redis client.
* **phpstan/phpstan**: analysis tool for PHP code, to detect and fix issues,
* **friendsofsymfony/rest-bundle**: simplifies the implementation of RESTful APIs.
* **symfony/serializer**: serialization system for converting between objects and arrays, to JSON, XML, YAML, and CSV.
* **stof/doctrine-extensions-bundle**: doctrine extensions, features to ORM entities (Timestampable)

***

## Getting Started
Copy or rename the `.env.dist` files (for docker and symfony) to an environment variable file and edit the entries to your needs:
```
cp ./app/.env.dist ./app/.env && cp .env.dist .env
```
### Run using composer

`composer run` commands are provided as **shortcuts**.

Use `composer run setup` to start and initialize all needed containers.

Available commands are:
```
composer run [
    setup             --- Build the docker images and run the containers in the background.
    build             --- Build the docker images.
    up                --- Run the containers in the background.
    down              --- Stop the containers.
    logs              --- Show container sys logs (php-fpm, nginx, and MariaDB).
    cache-clear       --- Execute Symfony clear cache command.
    stan              --- Execute PHPStan analyse command.
    test              --- Execute PHPUnit test cases.
    del-timestamp     --- Delete Redis timestamp key.
]
```

A folder named `var` will be created in the project root folder upon the first run. This folder includes the database files and server logs to provide help while developing.

### Run using docker
Alternatively to the use of `composer`, you can directly build & run the app by using the following docker commands:

* Use `docker compose` to start your environment.
    * Add the _param_ `-d` if you wish to run the process in the background.
    * Add the _param_ `--build` the **first time** to build the images.
    * Add the _keyword_ `down` to stop the containers.
```
# Build & up. From the project's root folder exec
docker-compose up -d --build
```

#### IMPORTANT
After booting the container, run `composer install` from outside or inside the container.
```
docker exec -t php-fpm composer install
```
Then run the database migrations to create the mysql structure for both **dev** and **test** environments.
```
docker exec -t php-fpm php bin/console doctrine:migrations:migrate --env=dev --no-interaction
```

```
docker exec -t php-fpm php bin/console doctrine:database:create --env=test --no-interaction
docker exec -t php-fpm php bin/console doctrine:migrations:migrate --env=test --no-interaction
```

You can use this command to enter inside it and execute commands (the container's name is defined in the _**docker-compose.yml**_ file):
```
docker exec -it $container_name bash
```
or identify the name of it displayed under the column `NAMES` of this command output:
```
docker ps
```
There's an alias being created upon the build process, and it will allow you to execute the Symfony command directly only with `sf`. Example:
``
sf debug:router
``

***

## How it works?
You have up to 5 containers running: php-fpm + nginx, mariadb, redis, and optionally, adminer.
Check the running containers by using the command: ``docker ps``
- [Symfony Web-App welcome page](http://localhost:80)
- [Adminer [optional] (simple database manager)](http://localhost:8080)


#### API
Use Postman or another CLI to perform actions on each endpoint.
A [postman collection](https://github.com/mangelperis/PMS-API/blob/main/PMS.postman_collection.json) is provided with the project with the source data endpoint and the destination one.

The list of available endpoints can be shown by executing (target **php-fpm** container):
```
docker exec php-fpm php bin/console debug:router
```
Provided endpoint is: 
```
  Name                            Method    Path                        Description
 ------------------------------- -------- ---------------------------- --------------------------------
  get_booking_by_room_and_hotel   GET       /api/booking?hotel=&room=    Returns the requested booking
 ------------------------------- -------- ---------------------------- --------------------------------
```

#### PHPUnit Testing
Additionally, run all the tests available using (target **php-fpm** container):
```
docker exec php-fpm ./vendor/bin/phpunit --verbose
```
or
```
composer test
```

**Important:** both dev and test environment use the same Redis service, this means both will set the same timestamp key.
Keep that in mind when running the tests, you should delete the timestamp key after to do not conflict with the dev environment.

```
docker exec -t redis redis-cli del pms:booking:created
```

***

#### xDebug debugger
xDebug (the last version) is installed and ready to use. Check the config params in `/docker/extras/xdebug.ini`
By default, these are the main critical parameters provided:
+ [mode](https://xdebug.org/docs/all_settings#mode) = develop,debug
+ [client_host*](https://xdebug.org/docs/all_settings#client_host) = host.docker.internal
+ [client_port](https://xdebug.org/docs/all_settings#client_port) = 9003
+ [idekey](https://xdebug.org/docs/all_settings#idekey) = PHPSTORM
+ [log_level](https://xdebug.org/docs/all_settings#log_level) = 0

Please check the [official documentation](https://xdebug.org/docs/all_settings) for more info about them.
Add the call to `xdebug_info()` from any PHP file to show the info panel.

####  __client_host__ (*)
Depending on your environment, it's **required** to add the following to the **_docker-composer.yml_** file to enable
communication between the container and the host machine. By default, this is **ON**.
```
extra_hosts:
    - host.docker.internal:host-gateway
```
If you find it's not working after setting up your IDE, try to comment on section and change the [xDebug.ini file](/docker/extras/xdebug.ini)
accordingly.

***

## Troubleshooting
Nothing else for now!