#!/usr/bin/env bash

export APP_CONTAINER=app
export COMPOSE_FILE=docker-compose.yml


function display_help {
    echo "Usage:";
    echo "  command [arguments]"
    echo "";
    echo "command:";
    echo "  help            Display help for a command";
    echo "  init            Initialize this Project";
    echo "  destroy         Destroy this Docker Environment (Contaners, Volumes, Network)";
    echo "  destroy-all     Destroy this Docker Environment (Contaners, Volumes, Network, Images and vendor/*)";
    echo "  migrate         Exec Database migration";
    echo "  refreshdb       Exec Database Refresh (remigrate & seed)";
    echo "  tinker          Exec php artisan tinekr";
    echo "  artisan         Exec php artisan";
    echo "  test            Display help for a command";
    echo "  ut              Exec Unit Test (phpunit)";
    echo "  ft              Exec Feature Test (phpunit)";
    echo "  [others]        Pass the arguments to 'Docker command'";
    echo "";
    exit 0;
}

# applicationディレクトリに移動
cd app

if [ $# = 0 ]; then
    # 引数なしの場合, command helpを表示
    display_help

elif [ "$1" = "help" ]; then
    # command helpを表示
    display_help

elif [ "$1" == "migrate" ]; then
  # Exec Database Migrate
  shift 1
  docker-compose exec ${APP_CONTAINER} php artisan migrate $@

elif [ "$1" == "refreshdb" ]; then
  # Exec Refresh Database
  docker-compose exec ${APP_CONTAINER} php artisan migrate:refresh
  docker-compose exec ${APP_CONTAINER} php artisan db:seed

elif [ "$1" == "tinker" ]; then
  # php artisan tinker
  docker-compose exec ${APP_CONTAINER} php artisan tinker

elif [ "$1" == "artisan" ]; then
  # php artisan
  shift 1
  docker-compose exec ${APP_CONTAINER} php artisan $@

elif [ $1 = "test" ]; then
  # Exec Test
  shift 1
  docker-compose exec ${APP_CONTAINER} sh -c "php artisan config:clear && php artisan test --stop-on-failure $@"

elif [ $1 = "ut" ]; then
  # Exec Test (UnitTest)
  shift 1
  docker-compose exec ${APP_CONTAINER} sh -c "php artisan config:clear && php artisan test --stop-on-failure --testsuite=Unit $@"

elif [ $1 = "ft" ]; then
  # Exec Test (FeatureTest)
  shift 1
  docker-compose exec ${APP_CONTAINER} sh -c "php artisan config:clear && php artisan test --stop-on-failure --testsuite=Feature $@"

elif [ $1 = "init" ]; then
  # Initialize Project
  if [ ! -e .env ]; then
    cp .env.example .env
  fi
  docker-compose up -d
  docker-compose exec ${APP_CONTAINER} composer install
  echo "waiting for database..."
  sleep 10
  docker-compose exec ${APP_CONTAINER} php artisan migrate
  docker-compose exec ${APP_CONTAINER} php artisan migrate --database=mysql_testing
  docker-compose exec ${APP_CONTAINER} php artisan db:seed

elif [ $1 = "destroy" ]; then
  # Destroy Docker Environment
  docker-compose down --volumes --remove-orphans

elif [ $1 = "destroy-all" ]; then
  # Destroy Docker Environment (ALL)
  docker-compose down --rmi all --volumes --remove-orphans
  sudo rm -rf ./vendor

else
    docker-compose "$@"
fi

