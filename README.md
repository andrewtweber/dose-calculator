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
