# P2P TRADING

P2P-Trading is a platform where can buy and sell cryptocurrency.


## Getting Started

1. To install and run the application locally, clone the project by running

```bash
git clone git@github.com:VictorUkafor/p2p-trading.git
```
2. cd into the root of the cloned project and run 

```bash
composer update
```
to install all required packages

3. Make a copy of .env.example file in .env by run

```bash
cp .env.example .env
```
and provide all required variables

4. To generate the app encryption key run 

```bash
php artisan key:generate
```

5. Run migration

```bash
php artisan migrate
```

6. Finally start the app by running

```bash
php artisan serve
```

## Hosted app

The hosted app is deployed to https://p2p-trading.herokuapp.com/api/documentation


## Author

Victor Ukafor victorukafor@gmail.com


## Acknowledgement

Daniel Oti - https://www.buycoins.africa

## Contributing


