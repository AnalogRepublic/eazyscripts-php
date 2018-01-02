test:
	vendor/bin/phpunit

release:
	sed -i.bak "s/\"version\": \".*\"/\"version\": \"$(version)\"/g" ./composer.json && \
	git add ./composer.json && \
	git commit -m "Bump to version $(version)" && \
	git tag -a $(version) -m "$(version)" && \
	git push && git push --tags
