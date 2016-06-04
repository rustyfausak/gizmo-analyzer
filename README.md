### Requirements

- PHP >= 5.5
- MySQL

### Configure

- `composer install`
- configure `propel.json` with database credentials
- `php vendor/propel/propel/bin/propel.php config:convert`

### Reset Database

- `php vendor/propel/propel/bin/propel.php sql:build`
- `php vendor/propel/propel/bin/propel.php sql:insert`
- `php vendor/propel/propel/bin/propel.php model:build`

or

- run `reset-db.bat`

### Analyze Replay JSON

- `php test.php <replay>`
