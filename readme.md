# POC Chiffrage Pattern
Tool to evaluate a workload in relation to the difficulty of different tasks.

## Prerequisite

* PHP 7.4+

## Install
`composer install`

Or you can add this poc like a dependency, in this case edit your [composer.json](https://getcomposer.org) (launch `composer update` after edit):
```json
{
  "repositories": [
    { "type": "git", "url": "git@github.com:jgauthi/poc_chiffrage_pattern.git" }
  ],
  "require": {
    "jgauthi/poc_chiffrage_pattern": "1.*"
  }
}
```

## Usage
You can test with php internal server and go to url http://localhost:8000 :

```shell script
php -S localhost:8000 -t public
```


## Documentation
You can look at [folder public](https://github.com/jgauthi/poc_chiffrage_pattern/tree/master/public).
