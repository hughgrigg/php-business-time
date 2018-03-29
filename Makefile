.PHONY: test
test: src tests vendor
	./vendor/phpmd/phpmd/src/bin/phpmd src text \
		controversial,design,naming,unusedcode
	./vendor/squizlabs/php_codesniffer/bin/phpcs --standard=PSR2 --colors src
	./vendor/phpunit/phpunit/phpunit

vendor: composer.lock
	composer install

.PHONY: reports
reports: src tests vendor
	mkdir -p reports
	rm -rf reports/*
	./vendor/phpmd/phpmd/src/bin/phpmd src text \
			controversial,design,naming,unusedcode \
			--reportfile ./reports/phpmd.xml
	./vendor/squizlabs/php_codesniffer/bin/phpcs --standard=PSR2 --colors src \
		--report-file=./reports/phpcs.xml
	./vendor/phpunit/phpunit/phpunit --coverage-clover=reports/coverage.xml \
		--coverage-html=reports/coverage
