.PHONY: test
test: src tests vendor
	./vendor/phpmd/phpmd/src/bin/phpmd src text \
		controversial,design,naming,unusedcode
	./vendor/squizlabs/php_codesniffer/bin/phpcs --standard=PSR2 --colors src
	./vendor/phpunit/phpunit/phpunit

vendor: composer.lock
	composer install

.PHONY: coverage
coverage: src tests vendor
	mkdir -p build
	rm -rf build/*
	./vendor/phpmd/phpmd/src/bin/phpmd src text \
			controversial,design,naming,unusedcode \
			--reportfile ./build/phpmd.xml
	./vendor/squizlabs/php_codesniffer/bin/phpcs --standard=PSR2 --colors src \
		--report-file=./build/phpcs.xml
	./vendor/phpunit/phpunit/phpunit --coverage-clover=build/logs/clover.xml \
		--coverage-html=build/coverage
