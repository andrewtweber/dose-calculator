# Dose Calculator

[![CircleCI](https://dl.circleci.com/status-badge/img/gh/andrewtweber/dose-calculator/tree/master.svg?style=shield)](https://dl.circleci.com/status-badge/redirect/gh/andrewtweber/dose-calculator/tree/master)

## About

Little package to make calculating medicine doses easier. Literally just made this a package so I could
reuse it between two projects.

## Installation

Install this package as a dependency using [Composer](https://getcomposer.org).

``` bash
composer require andrewtweber/dose-calculator
```

## Usage

Implement the `MedicineContract`. Doses should be stored in "mg/kg", concentrations in "mg/mL".
Weights are input in "kg". The doses are returned as a `DoseResult` object, with min and max values
and a unit a measurement.

```php
$medicine = new Medicine(
    min_dose: '20mg/kg',
    max_dose: '40mg/kg',
    concentration: '50mg/mL',
);
$calculator = new DoseCalculator($medicine);

// Returns minimum dose by default
$calculator->calculate(0.834); 
// "0.33"

// Specify precision
$calculator->calculate(0.834, final_precision: 3);
// "0.334"

// Maximum dose
$calculator->calculate(0.834, max: true);
// "0.67"

// Get the min-max range
$calculator->calculateRange(0.834);
// "0.33 - 0.67"

// Specify precision for range
$calculator->calculateRange(0.834, final_precision: 4);
// "0.3336 - 0.6672"
```

## Testing

```
phpunit
```
