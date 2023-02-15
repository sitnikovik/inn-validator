# INN Validator

A simple toolkit to validate individual taxpayer Numbers (abbr. INN) with validation algorithm.

Validation based on the algorithm described in [Wikipedia](https://ru.wikipedia.org/wiki/%D0%9A%D0%BE%D0%BD%D1%82%D1%80%D0%BE%D0%BB%D1%8C%D0%BD%D0%BE%D0%B5_%D1%87%D0%B8%D1%81%D0%BB%D0%BE#%D0%9D%D0%BE%D0%BC%D0%B5%D1%80%D0%B0_%D0%98%D0%9D%D0%9D).

There is only one public static method to check the number you need.

Works with numbers of individuals (12 numbers) or companies (10 numbers).

### Usage
```php
// Check individuals
InnValidator::validate('500100732259'); // returns true
InnValidator::validate('500111732259'); // returns false

// Check companies
InnValidator::validate('7830002293'); // returns true
InnValidator::validate('7830002294'); // returns false
```