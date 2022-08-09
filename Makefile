install:
		composer install
validate:
		composer validate
lint:
		composer exec --verbose phpcs -- --standard=PSR12 src bin
dump:
		composer dump-autoload
test:
	composer exec --verbose phpunit tests
test-coverage:
	composer exec --verbose phpunit tests -- --coverage-clover build/logs/clover.xml
demo-default:
	php bin/gendiff tests/fixtures/fileRecursive.json tests/fixtures/fileRecursive2.yaml
demo-plain:
	php bin/gendiff -f plain tests/fixtures/fileRecursive.json tests/fixtures/fileRecursive2.yaml
