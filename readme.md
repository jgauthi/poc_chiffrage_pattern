# POC Chiffrage Pattern
Tool to evaluate a workload in relation to the difficulty of different tasks; this tool returns from a tag, a number of hours.

## Prerequisite

* PHP 5.6 (v1), 7.4+ (v2)

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
You can test and evaluate your chiffrage tags with php internal server and go to url http://localhost:8000 :

```shell script
php -S localhost:8000 -t public
```

You can set your rules for evaluate time or use the default file [chiffrage.ini](https://github.com/jgauthi/poc_chiffrage_pattern/blob/master/config/chiffrage.ini) _(don't set the `$chiffrageIniFile` argument on Pattern constructor)_.

**Example Rules:**

**Name** | **Tag** | **Time** (hour) | **Time additional**
----        | ---  | --- | ---
feature     | _f_  | 4h  | 3h
menu        | _m_  | 2h  | 1h
template    | _t_  | 4h  | 2.5h
...         | | | 

The tool allows you to think about a task according to **its nature and its level of difficulty**, instead of evaluating the time necessary for its accomplishment. A rule defines a type of task, the time it would generally take. If the difficulty is high, additional time is added during the calculation which will take this task.

For example: _£ma2_, this tag mean: **M**enu **A**rticle difficulty 2.
* The first word is the name of the rule _(the letter associate must be unique on ini file)_,
* the second is a type of content (like article, configuration, page, service, theme) which is indicative without impacting the calculation.
* The number represents the difficulty (optional), from 0 (no difficulty, default value if not set), 1 (low) to 9 (very high).

In case on £ma2, the tool calculate:
* Initial time: 2h
* Additional time: _difficulty * additional_: 2 * 1 = 2h
* Total: _Initial time + Additional time_ = 2 + 2 = **4h**


## Documentation
You can look at [folder public](https://github.com/jgauthi/poc_chiffrage_pattern/tree/master/public).
