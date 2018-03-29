.PHONY: test
test: vendor
	./vendor/phpmd/phpmd/src/bin/phpmd src text \
		controversial,design,naming,unusedcode
	./vendor/squizlabs/php_codesniffer/bin/phpcs --standard=PSR2 --colors src
	./vendor/phpunit/phpunit/phpunit

vendor: composer.lock
	composer install
