#!/bin/bash

home_dir=$HOME

# Install composer if not installed
if ! command -v composer &> /dev/null; then
  echo "Installing composer..."
  php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
  php composer-setup.php
  php -r "unlink('composer-setup.php');"
  sudo mv composer.phar /usr/local/bin/composer
fi

# Install symfony-cli if not installed
if ! command -v symfony &> /dev/null; then
  echo "Installing symfony-cli..."
  curl -sS https://get.symfony.com/cli/installer | bash
  sudo mv $home_dir/.symfony5/bin/symfony /usr/local/bin/symfony
fi

# Execute composer install
echo "Installing PHP components using composer ..."
composer install

# Execute npm install
echo "Installing Js dependencies"
npm install

# Execute npm run dev
echo "Installing assets ..."
npm run dev

# Loading database
echo 'Loading database ...'
bin/console doctrine:schema:update --force -q
bin/console doctrine:fixtures:load -q


# Execute symfony server:start
echo "Installing SSL Certificare and Starting Symfony server..."
symfony server:ca:install
symfony server:start
