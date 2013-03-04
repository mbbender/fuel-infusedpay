# InfusedPay
## A payment processor package for the fuel framework.
InfusedPay can be used to integrate common payment gateways for the purposes of processing
credit card payments, issuing refunds and voiding transactions with common payment gateways. It
also has a well defined interface for adding your own payment gateways if one is not already supported.

## Supported Gateways
- Authorize.net
- Paypal

## Installation
Add the git submodule

    `git submodule add git://github.com/michael-bender/infusedpay.git`

Run the migration (make sure you have your package path set in config FuelPHP V < 2)

    `php oil r migrate --packages=infusedpay`

Now go use the dern thang!

## Common Usage
To process transactions you need a model that implements the TransactionProcessor interface.

## Add your own gateway
 * Create an adapter in classes > adapter and implement all required functions
 * Create a model in responses of the like named adapter and a migration script to add the database table for raw gateway response logging

## Fully Tested
To run tests you must configure the authorizenet.php config with valid test system credentials, and if you have changed the
 FUEL_ENV variable you must change it back to TEST or DEVELOPMENT (It will automatically be TEST if you haven't overriden it)
 and then run

test group InfusedPay

    `$ php oil test --group=InfusedPay`


