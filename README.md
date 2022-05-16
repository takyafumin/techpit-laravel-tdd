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

```bash
./run.sh init
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
|  |
|  +----- docker/               # Docker
|  |
|  +---- docker-compose
|
+----- README.md
```

