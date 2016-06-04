- configure `propel.json` with database credentials
- run `php vendor/propel/propel/bin/propel.php config:convert`

To reset the database:

- `php vendor/propel/propel/bin/propel.php sql:build`
- `php vendor/propel/propel/bin/propel.php sql:insert`
- `php vendor/propel/propel/bin/propel.php model:build`

or

- run `reset-db.bat`

To run:

- `php test.php <replay>`
