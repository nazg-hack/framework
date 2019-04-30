.PHONY: preload
preload:
	hhvm ./vendor/bin/hh-autoload.hack

.PHONY: test
test:preload
	hhvm ./vendor/bin/hacktest.hack tests/
