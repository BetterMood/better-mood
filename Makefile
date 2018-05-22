MOODLE_FULLNAME="Docker moodle"
MOODLE_SHORTNAME="docker_moodle"
MOODLE_ADMINPASS="test"
MOODLE_ADMINEMAIL="admin@example.com"

dockerUp:
	docker-compose up -d

dockerDestroy:
	docker-compose stop && docker-compose rm -f

waitForDb: #wait 10 seconds until the database is ready to connect
	sleep 10

devInstall: dockerUp waitForDb
	if [ ! -f config.php ]; then \
		cp config.dev.php config.php; \
	fi; \
	docker-compose exec php php admin/cli/install_database.php --agree-license --fullname=$(MOODLE_FULLNAME) \
	--shortname=$(MOODLE_SHORTNAME) --adminpass=$(MOODLE_ADMINPASS) --adminemail=$(MOODLE_ADMINEMAIL)

behatInstall:
	docker-compose exec behat php admin/tool/behat/cli/init.php --parallel=2
behatRun:
	docker-compose exec behat php admin/tool/behat/cli/run.php

behat: dockerUp waitForDb behatInstall behatRun


legacyPhpunitInstall:
	docker-compose exec phpunit php admin/tool/phpunit/cli/init.php
legacyPhpunitRun:
	docker-compose exec phpunit php vendor/bin/phpunit -c phpunit.legacy.xml

legacyUnit: dockerUp waitForDb legacyPhpunitInstall legacyPhpunitRun

phpunitRun:
	docker-compose exec phpunit php vendor/bin/phpunit -c phpunit.xml.dist

unit: dockerUp phpunitRun