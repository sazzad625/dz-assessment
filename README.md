#PREPARATIONS
Create a set of new directories inside ~/Code to put the project we are building.

`$ mkdir -p foo-com/docker/services/{app,web} foo-com/docker/volumes/{mysql,redis} foo-com/src`  
`$ cd foo-com`  
`$ tree`  
This should give you the following directory structure:

`$ tree`  

```
├── docker
│   ├── services
│   │   ├── app
│   │   └── web
│   └── volumes
│       ├── mysql
│       └── redis
└── src

8 directories, 0 files```  

Hey! Calling tree gives me command not found: tree
Solve it by installing the package via Homebrew → brew install tree

SOURCE CONTROL
It is very likely that you are going to use Git as your source control, hence some extra steps are needed. Within both mysql and redis directories, create new empty file and name it .gitkeep.

touch docker/volumes/mysql/.gitkeep
touch docker/volumes/redis/.gitkeep
Now in the root create .gitignore file with the following content:

!docker/volumes/mysql
docker/volumes/mysql/*
!docker/volumes/mysql/.gitkeep

!docker/volumes/redis
docker/volumes/redis/*
!docker/volumes/redis/.gitkeep

.DS_Store
.idea
 What this is telling us?
We use Docker volumes as a way to persist our data. This setup guarantees that once we shut down Docker containers or even turn off our host machine, no data will be lost. Obviously we don't want any clutter to be part of the repository, hence we simply ignore the content within (not the directories themselves). Last two lines are optional and depend on both IDE and OS.

DOCKER SERVICES
This is the core part of this article.

Let's start with the main app.dockerfile service.

touch docker/services/app/app.dockerfile
Content of the app.dockerfile:

FROM php:8.1-fpm

WORKDIR /var/www

RUN apt-get update && apt-get install -y \
    build-essential \
    curl \
    git \
    jpegoptim optipng pngquant gifsicle \
    locales \
    unzip \
    vim \
    zip

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions

# Graphics Draw
RUN apt-get update && apt-get install -y \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

# Multibyte String
RUN apt-get update && apt-get install -y libonig-dev && docker-php-ext-install mbstring

# Miscellaneous
RUN docker-php-ext-install bcmath
RUN docker-php-ext-install exif
RUN docker-php-ext-install pdo_mysql

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install specific version of Node.js with npm through nvm
SHELL ["/bin/bash", "--login", "-c"]
RUN curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.1/install.sh | bash
RUN nvm install v16.17.0

# Install Cron
RUN apt-get update && apt-get install -y cron
RUN echo "* * * * * root php /var/www/artisan schedule:run >> /var/log/cron.log 2>&1" >> /etc/crontab
RUN touch /var/log/cron.log

CMD bash -c "cron && php-fpm"
 Explain please?
Our Laravel application will be based on PHP 8.1-fpm image
We define a WORKDIR which represents the relative path to the destination location for the app's files
We install both mandatory tools that are needed down the line
We install essential PHP extensions (needed by the framework)
Graphics Draw Library for image creation and processing
Multibyte String Library is required by Laravel along with other extensions like bcmath or pdo_mysql. Our list does not contain all of the extensions listed by the official Laravel documentation, because most of them are already installed and enabled by default. Later we can always jump into the app's container and check available extensions by using php -m
We install Composer as this is our backend package manager
We install Node.js to be able to use the npm package manager to work with our frontend assets. This was surprisingly the most painful part to define in this file. 🤮 I wanted to have full control over the version of Node.js I was going to install, however none of the solutions on how to install it worked as expected with the PHP image I have picked. 🥴 After multiple attempts, the easiest way was to use additional tool called nvm, to install a specific version of Node.js (with no issues down the line) 🎉
Finally, we want to have Laravel Scheduler up and running in the background. We install the cron package, next we add the only entry into crontab we need. Lastly we create the log file so that we can tail it when needed
Now, create 2 essential files for the web service.

touch docker/services/web/web.dockerfile
touch docker/services/web/vhost.conf
Content of the web.dockerfile:

FROM nginx:1.21

COPY vhost.conf /etc/nginx/conf.d/default.conf

RUN ln -sf /dev/stdout /var/log/nginx/access.log
RUN ln -sf /dev/stderr /var/log/nginx/error.log
Content of the vhost.conf:

server {
    listen 80;
    index index.php index.html;
    root /var/www/public;

    location / {
        try_files $uri /index.php?$args;
    }

    location ~ \.php$ {
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass app:9000;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param PATH_INFO $fastcgi_path_info;
    }
}
 Explain please?
Our web server (service) will be based on Nginx 1.21 image
We copy server settings (vhost.conf) into container's location (where the default file is located) so that we override it
Server is going to listen on port 80
Server's root directory is the exact location of Laravel's public directory
We establish a sort of a connection between web service and the app service that will listen on port 9000 for incomming PHP requests (fastcgi_pass app:9000)
We create 2 symbolic links to output logs into a file
MAIN DOCKER-COMPOSE.YML FILE
In the root type:

touch docker-compose.yml
As content:

version: "3.8"
services:
  # Application
  app:
    build:
      context: ./docker/services/app
      dockerfile: app.dockerfile
    working_dir: /var/www
    volumes:
      - ./src:/var/www
  # Web Server
  web:
    build:
      context: ./docker/services/web
      dockerfile: web.dockerfile
    working_dir: /var/www
    volumes:
      - ./src:/var/www
    ports:
      - "80:80"
  # Database
  database:
    image: mysql:8.0.25
    volumes:
      - ./docker/volumes/mysql:/var/lib/mysql
    environment:
      MYSQL_DATABASE: ${DB_DATABASE}
      MYSQL_ROOT_PASSWORD: ${DB_PASSWORD}
      MYSQL_PASSWORD: ${DB_PASSWORD}
      MYSQL_USER: ${DB_USERNAME}
    ports:
      - "3306:3306"
  # Database Management
  pma:
    image: phpmyadmin:5.1
    environment:
      - PMA_ARBITRARY=1
      - PMA_HOST=${DB_HOST}
      - PMA_USER=${DB_USERNAME}
      - PMA_PASSWORD=${DB_PASSWORD}
      - PMA_PORT=${DB_PORT}
    depends_on:
      - "database"
    ports:
      - "8888:80"
  # Caching
  redis:
    image: redis:alpine
    volumes:
      - ./docker/volumes/redis:/data
    ports:
      - "6379:6379"
  # Mailing Server
  mailhog:
    image: mailhog/mailhog:latest
    logging:
      driver: "none"
    ports:
      - "1025:1025"
      - "8025:8025"
 Let's finally break down this one…
This is the main YAML file where we define all:

services
networks
volumes
for the multi-container Docker application we are creating.

There are actually only two main sections of this file:

version - we specify version of the Compose file to be 3.8. Follow the link to explore this topic further
services - we define all of containers that combined together create our multi-containerized Laravel app
Application app
Web Server web
Database database
Database Management pma
Caching redis
Mail Server mailhog
Application is build upon local app.dockerfile file. The key thing here is to define a volume between local src directory and /var/www within a container, as this is the place for a sample application.

Web Server same as before, local web.dockerfile file is the source to create a Docker image. We define the same type of volume as before, but more importantly we specify on which port our web server with Laravel will be running. Because sometimes macOS can take over port 80, for safety we will use 8080 (left side) on the host. This will point to port 80 (right side) within a container. To ensure that all of the essential services are available before we boot the web service, we specify the depends_on key with list of other services, that Docker will raise prior to the web service.

Database is build based on external image of a particular version of MySQL. We mount volume to keep any data persistent between container runs. This mounts host's ./docker/volumes/mysql into container's /var/lib/mysql. In terms of the port number, we pick 3306 for both ends.

 Where the credentials are coming from?
We are going to use Laravel's .env file (./src/.env). Carry on reading and everything will become clear.

Database Management is an optional container to conveniently manage Laravel's database. Since phpMyAdmin is a browser tool, we specify another port (8888) on the host to point to port 80 within a container. If you prefer to use external application to connect to the database, remember to use 127.0.0.1 as a host address. Remaining credentials will be taken (as mentioned before) from the .env file. This container obviously depends_on the database.

Caching is pulled from the alpine version of the Redis image. Same as in case of the database, we mount host's ./docker/volumes/redis into container's /data to persist any cached data.

Mailing Server is the latest Mailhog Docker image. We specify a pair of ports. 1025 for the SMTP server and 8025 for the HTTP server because Mailhog is a web tool, similarly to phpMyAdmin. We also specify that we do not want a container to store any logs. This is a purely disposable service.

APPLICATION SOURCE
