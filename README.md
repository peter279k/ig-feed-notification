# ig-feed-notification

## Introduction

This is the simple web service and it can send the latest Instagram feed with specific user via SMTP server.

## Requirement

- php>=5.6
- Composer
- SMTP server
- MySQL

## Usage

- Clone the repository.

```
git clone https://github.com/peter279k/ig-feed-notification
```

- Download composer and install the required dependencies.

```
php composer.phar install -n
```

- Copy the `.env.example` to `.env` and set the mail host, user name and password.

```
cp .env.example .env
```

- Copy the `phinx.yml.example` to `phinx.yml` and remember to set the database name, user name and password.

```
cp phinx.yml.example phinx.yml
```

- Execute the database migration in the project root path.

```
php vendor/bin/phinx migrate
```

- Visit the `index.php` via the web browser.

```
http://host-name-or-ip-address/ig-feed-notification/index.php
```

- That's all. Enjoy it!
