Laravelでテスト駆動開発を学ぼう！
==============================

Laravelを使ってTDD開発を行うサンプル


Description(概要)
----------


Requirement(依存)
----------

* git
* docker


How To Use(使い方)
----------

環境設定ファイルを作成し, docker環境を構築します

```bash
# 環境設定ファイルをコピーして作成
cp docker/.env.example docker/.env
cp app/.env.example app/.env


cd docker

# containerのbuild & up
docker-compose up -d

# composer install
docker-compose exec app composer install

# db migration
docker-compose exec php artisan migrate
docker-compose exec php artisan migrate --database=mysql_testing
```


### Laravel Project Initialize

```bash
# into app container
cd docker
docker-compose exec app ash

# install laravel project
composer create-project --prefer-dist "laravel/laravel=8.*" .
```


Structure(ディレクトリ構造)
----------

```bash
.
+----- app/                     # Laravel Application
+----- docker/                  # Docker
|  |
|  +---- docker-compose
|
+----- README.md
```

