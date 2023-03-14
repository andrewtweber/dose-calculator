# Dose Calculator

(CircleCI)

## About

Dose Calculator

## Installation

Install this package as a dependency using [Composer](https://getcomposer.org).

``` bash
composer require andrewtweber/dose-calculator
```

## Usage

Implement the `MedicineContract`. Doses should be stored in "mg/kg", concentrations in "mg/mL".
The doses are returned in "mL" but only the numerical value is returned.

```php
$calculator = new DoseCalculator($medicine);

// Calculate minimum dose for a patient that weighs 1.2 kg
$calculator->calculate(1.2, max: false);
// "0.25"

// Calculate minimum dose for a patient that weighs 1.2 kg with specified precision
$calculator->calculate(1.2, max: false, final_precision: 1);
// "0.3"

// Calculate maximum dose for a patient that weighs 1.2 kg
$calculator->calculate(1.2, max: true);
// "0.5"

// Calculate min-max range for a patient that weighs 1.2kg
$calculator->calculateRange(1.2);
// "0.25 - 0.5"
```

## Testing

```
phpunit
```
