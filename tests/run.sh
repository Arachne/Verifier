./vendor/bin/phpcs -p --standard=vendor/arachne/coding-style/ruleset.xml src
./vendor/bin/phpcs -p --standard=vendor/arachne/coding-style/ruleset.xml --ignore=_temp tests
./vendor/bin/codecept build
./vendor/bin/codecept run Unit
./vendor/bin/codecept run Integration
