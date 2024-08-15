## Introduction

This is a Crowdfactoring decentralized application built using Soroban Smart-Contracts as a web3 technology and PHP, Symfony and React as a web2 technology. This Dapp uses a web2 approach to manage users' authentication and authorization and to register contracts and transactions in a PostgreSQL database once they have been successfully sent to Stellar. The web3 part works differently depending on the user role:

- **Companies**: Companies deploy and initialize contracts. Each contract represents a project in which a user can invest. The contract code manages users' balances, allows sending deposits and claiming withdrawals, and calculates the interest the user will earn. In this case, the communication between the web2 part and the stellar blockchain is made in a custodial way. The platform keeps a Stellar keyPair which is used to deploy, install and initialize contracts. Then, each contract address is linked to a company using a PostgreSQL table.
- 
- **Users**: Users can send deposits to the available contracts so that they can invest in company projects and make their capital profitable. This part works in a non-custodial way, that is, users must use their wallets (for instance, freighter) to approve and sign the transactions. This gives users more control and removes the need to use intermediaries.

## Installation instructions

### Requirements
This installation instructions have been tested from a Ubuntu 22.04 system. Besides, this project uses a docker-compose yaml file to load the database so having [docker-compose](https://docs.docker.com/compose/) is required.

### Install PHP

This project requires PHP >= 8.2 to work. Run the following command to install php 8.2 and the required modules:

```shell
apt-get install php8.2 php8.2-fpm php8.2-intl php8.2-pgsql php8.2-xml php8.2-mbstring php8.2-pdo php8.2-gd php8.2-zip php8.2-curl php8.2-gmp php8.2-bcmatch
```

### Install Node

Node >= 18 and Npm are required to run this project (since it uses React as a frontend). A recommended way to install node is using Node Version Manager (nvm):

```shell
curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.7/install.sh | bash
nvm install 22
```

### Setup the application 

#### Automatic

Execute the setup.sh bash script to install the rest of the elements required. Execute it from the project root folder.

```shell
./setup.sh
```

This script will perform the following tasks:

- If [composer](https://getcomposer.org/) is not installed, It will install it.
- If [symfony-cli](https://symfony.com/download) is not installed, It will install it.
- Install PHP / Symfony components using "composer install"
- Install node dependencies using "npm install"
- Install [webpack encore](https://symfony.com/doc/current/frontend/encore/installation.html) assets using "npm run dev"
- Load PostgreSQL database by:
    - Starting PostgreSQL container using "docker-compose up -d"
    - Creating database schema using "doctrine:schema:update --force -q"
    - Populating database with basic fixtures using "doctrine:fixtures:load -q"
- Install Symfony SSL certificate using "symfony server:ca:install"
- Start local web server using: "symfony server:start"

Once setup.sh finishes, a local web server keeps listening on port 8000. You can now access the application using the link: https://127.0.0.1:8000

#### Manual

- If don't have composer installed in your computer, install it following the instructions [here](https://getcomposer.org/)
- If don't have symfony-cli installed in your computer, install it following the instructions [here](https://symfony.com/download)
- Go to the project root folder
- Execute "composer install"
- Execute "npm install"
- Execute "npm run dev"
- Execute "docker-compose up -d"
- Execute "doctrine:schema:update --force"
- Execute "doctrine:fixtures:load"
- Execute "symfony server:ca:install"
- Execute "symfony server:start"

Then, open the url https://127.0.0.1:8000

## Folder structure
As this dApp has been developed using the [Symfony framework](https://symfony.com/), it follows the framework folder organization. Let's enumerate and explain the most important folders step by step:

### The assets folder 
It contains all the frontend resources (react components, typescript files, css files etc). The *react/controllers* subfolder contains all the react components used by the application. The services subfolder contains other folders of which the following are the most relevant:
   
#### api 
Contains two files: web2.ts and contract.ts
   - **web2.ts**: This file connects to the dApp API
   - **contract.ts**: This file connects to Soroban using the JS Stellar SDK and the Stellar Wallets Kit. 
#### wallet
Contains a file named *wallet.ts* which holds a React hook to load a Stellar Wallet using the Stellar Wallets Kit.

#### soroban 
Contains two files: *auth.ts* and *token.ts*.
   - **auth.ts**: Holds the necessary code to sign auth entries. (This is not necessary since the *assembleTransaction* method of the *SorobanRpc.Api* module does the work).
   - **token.ts**: Normalizes an amount depending on the number of token decimals.


### The public folder
Contains the compiled assets and all frontend elements. They are compiled using the [Symfony Webpack Encore bundle](https://symfony.com/doc/current/frontend/encore/installation.html). 

### The src folder
Here is where all the backend code lives, including the code that communicates to Soroban using the [Soneso Stellar SDK](https://github.com/Soneso/stellar-php-sdk). Let's see the most important subfolders

#### The Api subfolder
Contains the services related to the API calls. Services for registering users, creating contracts, getting contracts etc

#### The Controller subfolder
Contains the controllers:

   - **PagesController**: Loads the dApp pages (rendering twig templates). These twig templates load the react components inside them thanks to the [Symfony React component](https://symfony.com/bundles/ux-react/current/index.html).
   - **ApiController**: Contains all the API calls. They are called from the React components and return the responses as JSON.
  
   > Both **PagesController** and **ApiController** are protected by the same firewall which configuration remains in the *config/security.yaml* file.

   - **LoginController**: Contains the necessary calls to log-in, logout and register a user. These calls are outside of the firewall.

#### The Dto folder
Contains some Data Transfer Objects to manage API inputs and outputs

#### Entity
Contains all the entities (managed by [doctrine](https://symfony.com/doc/current/doctrine.html)) used by the dApp.

#### Repositories
Contains the repository classes associated with each entity.

#### Event
Contains an event subscriber that listens to data validation exceptions to format the errors before returning the response to the client.

#### Stellar
Contains the services that connect to Soroban. Let's see it:

   - **DeployManager**: Located in the *Stellar/Soroban/Contract* folder, it is in charge of deploying a contract using the wasm file contents.
   - **InstallManager**: Located in the *Stellar/Soroban/Contract* folder, it is in charge of installing a contract given a wasm ID.
   - **InteractManager**: Located in the *Stellar/Soroban/Contract* folder, it is in charge of:
      - Initializing a contract
      - Stopping contract deposits
      - Getting contract balance
   - **WasmManager**: Located in the *Stellar/Soroban/Contract* folder, it is in charge of reading wasm file.     
   - **SorobanTransactionManager**: Located in the *Stellar/Soroban/Transaction* folder, it is in charge of simulating a transaction, adding auth entries and waiting until success or failure.
   - **ServerManager**: Located in the *Stellar/Soroban* folder, it is in charge of loading the server object and throwing an exception if the server is not healthy.
   - **AccountManager**: Located in the *Stellar* folder, it is in charge of getting and saving the system KeyPair to sign custodial transactions.
   - **Networks**: A PHP enum with the stellar test networks (Testnet and Futurenet).

### The templates folder
Contains the twig templates which are rendered by the Pages Controller routes.

### The wasm folder
Contains the smart contract wasm file