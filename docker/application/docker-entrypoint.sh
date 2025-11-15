#!/bin/bash

# region Установить переменные
APP_ENV=$(printenv APP_ENV)
SERVER_NAME=$(printenv SERVER_NAME)
PROJECT_PATH=$(printenv PROJECT_PATH)
APPLE_SILICON=$(printenv APPLE_SILICON)
# endregion

# region Установить Composer из GitHubs
composer --version

if [ $? -ne 0 ]; then
    echo -e '\nУстанавливаем Composer альтернативным способом\n'

    git clone https://github.com/composer/composer.git /composer
    cd /composer

    TAGS_STRING=$(git tag)

    # region Убрать пробелы
    TAGS_STRING=$(echo "$TAGS_STRING" | sed 's/^ *//g')
    # endregion

    # region Заменить переносы строк на «||»
    TAGS_STRING=${TAGS_STRING//$'\n'/\||}
    # endregion

    # region Разбить строку на массив по разделителю
    IFS='||' read -r -a TAGS_EMPTY_LINES <<< "$TAGS_STRING"
    # endregion

    # region Убрать пустые строки из массива
    for TAG in "${TAGS_EMPTY_LINES[@]}"; do
      if [[ -n "$TAG" ]]; then
        TAGS+=("$TAG")
      fi
    done
    # endregion

    # region Перевернуть массив
    reverse() {
        # first argument is the array to reverse
        # second is the output array
        declare -n arr="$1" rev="$2"
        for i in "${arr[@]}"
        do
            rev=("$i" "${rev[@]}")
        done
    }

    reverse TAGS TAGS_REVERSE
    # endregion

    for TAG in "${TAGS_REVERSE[@]}"
    do
        FIRST_CHARACTER=${TAG:0:1}

        if [[ $FIRST_CHARACTER -gt 1 ]] && [[ $TAG =~ ^[0-9.]+$ ]]
        then
            wget https://github.com/composer/composer/releases/download/$TAG/composer.phar
            chmod 755 composer.phar
            mv composer.phar /usr/local/bin/composer
            break
        fi
    done

    rm -rf /composer
    cd $PROJECT_PATH
fi
# endregion

# region Заменить параметры nginx параметрами из файла .env
envsubst '$SERVER_NAME $PROJECT_PATH' < /etc/nginx/conf.d/nginx.conf | tee /etc/nginx/conf.d/nginx-update.conf 2>&1 >/dev/null
mv -f /etc/nginx/conf.d/nginx-update.conf /etc/nginx/conf.d/nginx.conf
# endregion

# region Заменить параметры opcache параметрами из файла .env
envsubst '$PROJECT_PATH' < /usr/local/etc/php/conf.d/opcache-recommended.ini | tee /usr/local/etc/php/conf.d/opcache-recommended-update.ini 2>&1 >/dev/null
mv -f /usr/local/etc/php/conf.d/opcache-recommended-update.ini /usr/local/etc/php/conf.d/opcache-recommended.ini
# endregion

# region Отключить конфигурацию по умолчанию [стартовая страница nginx]
unlink /etc/nginx/sites-enabled/default
# endregion

# region Объявить каталог безопасным
git config --global --add safe.directory $PROJECT_PATH
# endregion

if [ ! -d $PROJECT_PATH/public/ ]
then
  # region Установить Symfony
  composer create-project symfony/skeleton app
  # endregion

  # region Перенести файлы Symfony в корневую категорию
  mv app/* . && rm -rf app
  # endregion

  # region Отменить добавление рецептов Symfony
  composer config --json extra.symfony.docker 'false'
  # endregion

  # region Установить пакеты
  composer require \
  symfony/orm-pack \
  symfony/serializer-pack \
  symfony/monolog-bundle \
  admin \
  php-ds/php-ds \
  guzzlehttp/guzzle \
  api \
  api-platform/graphql \
  lexik/jwt-authentication-bundle

  composer require --dev \
  maker-bundle \
  orm-fixtures profiler \
  codeception/module-symfony \
  codeception/module-rest \
  codeception/module-doctrine \
  codeception/module-phpbrowser \
  codeception/module-cli \
  codeception/module-asserts \
  codeception/c3
  # endregion

  # region Установить полные права на все папки и файлы
  cd $PROJECT_PATH && chmod -R 777 .
  # endregion

  # region Создать пустой файл миграции (инициализация ДБ)
  php bin/console make:migration
  # endregion
fi

# region Установить пакеты
composer install
# endregion

# region Сгенерировать карту класса
# composer dump-autoload --classmap-authoritative
# endregion

# region Очистить/прогреть кэш и накатить миграции
php bin/console cache:clear && php bin/console cache:warmup
php bin/console doctrine:migrations:migrate -n
php bin/console lexik:jwt:generate-keypair --skip-if-exists
# endregion

# region Настройка разрешений для приложений Symfony
# Статья: https://symfony.com/doc/7.3/setup/file_permissions.html#configuring-permissions-for-symfony-applications
# Ошибка: Unable to create the storage directory (/pub/www/app/var/cache/dev/profiler).
HTTPDUSER=$(ps axo user,comm | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1)

# region Если переменная пустая, то присваиваем ей значение по умолчанию
if [[ -z "$HTTPDUSER" ]]; then
   HTTPDUSER=www-data
fi
# endregion

setfacl -dR -m u:"$HTTPDUSER":rwX -m u:$(whoami):rwX var
setfacl -R -m u:"$HTTPDUSER":rwX -m u:$(whoami):rwX var
setfacl -dR -m u:"$HTTPDUSER":rwX -m u:$(whoami):rwX /tmp/symfony-cache
setfacl -R -m u:"$HTTPDUSER":rwX -m u:$(whoami):rwX /tmp/symfony-cache
# endregion

# region Условие, определяющее текущую машину как MAC OS на чипе M1
if [ -n "${APPLE_SILICON+x}" ] && [ "${APPLE_SILICON}" -eq "1" ]; then
  cd /
  wget https://github.com/drpayyne/mhsendmail/releases/download/v0.3.0/mhsendmail_linux_arm64
  chmod +x mhsendmail_linux_arm64
  mv mhsendmail_linux_arm64 /usr/local/bin/mhsendmail
  cd $PROJECT_PATH
fi
# endregion

# region Установить lefthook
npm install lefthook --save-dev
node_modules/.bin/lefthook install
# endregion

# region Запустить supervisor
supervisord -n -c /etc/supervisor/conf.d/supervisord.conf
# endregion
