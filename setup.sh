#!/bin/bash

home_dir=$HOME

# Check if PHP is installed and its version is >= 8.2
if command -v php &> /dev/null; then
  php_version=$(php -r "echo phpversion();")

  if [[ $php_version =~ ^8\.[2-9] ]]; then
    echo "PHP version $php_version is greater than or equal to 8.2" 
  else
    echo "PHP version $php_version is less than 8.2. Please install PHP >=8.2 before continuing"
    exit;
  fi
else
  echo "Please install PHP >=8.2 before continuing"
  exit;
fi

# Check if Node is installed and its version is >= 18
if command -v node &> /dev/null; then
  node_version=$(node -v)
  if [[ $node_version =~ ^v([0-9]+)\.([0-9]+)\.([0-9]+)$ ]]; then
    major_version=${BASH_REMATCH[1]}
    if [ $major_version -ge 18 ]; then
      echo "Node $node_version is already installed"
    else
      echo "Node >= 18 is not installed. Install a major version before continue"
      exit;
    fi
  else
    echo "Invalid Node version: $node_version"
    exit;
  fi
else
  echo "Node >= 18 is not installed. Install a major version before continue"
  exit;
fi

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
echo "Running composer install..."
composer2 install

# Execute npm install
echo "Running npm install..."
npm install

# Execute npm run dev
echo "Running npm run dev..."
npm run dev

# Loading database
bin/console doctrine:schema:update --force -q
bin/console doctrine:fixtures:load -q


# Execute symfony server:start
symfony server:ca:install
echo "Starting Symfony server..."
symfony server:start
