#!/bin/bash

home_dir=$HOME

# Check if Docker is already installed
if ! command -v docker &> /dev/null; then
  # Install Docker
  echo "Installing Docker..."
  sudo apt update
  sudo apt install -y ca-certificates curl gnupg lsb-release
  sudo mkdir -p /etc/apt/keyrings
  curl -fsSL https://download.docker.com/linux/ubuntu/gpg | sudo gpg --dearmor -o /etc/apt/keyrings/docker.gpg
  echo "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.gpg] https://download.docker.com/linux/ubuntu $(lsb_release -cs) stable" | sudo tee /etc/apt/sources.list.d/docker.list > /dev/null
  sudo apt update
  sudo apt install -y docker-ce docker-ce-cli containerd.io docker-compose-plugin
else
  echo "Docker is already installed."
fi

# Check if Docker Compose is already installed
if ! command -v docker-compose &> /dev/null; then
  # Install Docker Compose
  echo "Installing Docker Compose..."
  sudo apt install -y docker-compose
else
  echo "Docker Compose is already installed."
fi

# Verify the installation
echo "Verifying the installation..."
docker --version
docker-compose --version

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
docker-compose up -d
bin/console doctrine:schema:update --force -q
bin/console doctrine:fixtures:load -q


# Execute symfony server:start
echo "Installing SSL Certificare and Starting Symfony server..."
symfony server:ca:install
symfony server:start
