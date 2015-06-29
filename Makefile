# The following env variables need to be set:
# - VERSION
# - GITHUB_USER
# - GITHUB_TOKEN (optional if you have two factor authentication in github)

# Use the version number to figure out if the release
# is a pre-release
PRERELEASE=$(shell echo $(VERSION) | grep -E 'dev|rc|alpha|beta' --quiet && echo 'true' || echo 'false')
PLUGINS=Nodes Menus Taxonomy Users
CURRENT_BRANCH=$(shell git branch | grep '*' | tr -d '* ')

# Github settings
UPLOAD_HOST=https://uploads.github.com
API_HOST=https://api.github.com
OWNER=cvo-technologies
REMOTE=origin
GITHUB_ROOT=~/work/repo/croogo

ifdef GITHUB_TOKEN
	AUTH=-H 'Authorization: token $(GITHUB_TOKEN)'
else
	AUTH=-u $(GITHUB_USER) -p$(GITHUB_PASS)
endif

DASH_VERSION=$(shell echo $(VERSION) | sed -e s/\\./-/g)

ALL: help
.PHONY: help install test need-version bump-version tag-version

help:
	@echo "Croogo Makefile"
	@echo "================"
	@echo ""
	@echo "release"
	@echo "  Create a new release of CakePHP. Requires the VERSION and GITHUB_USER, or GITHUB_TOKEN parameter."
	@echo "  Packages up a new app skeleton tarball and uploads it to github."
	@echo ""
	@echo "package"
	@echo "  Build the app package with all its dependencies."
	@echo ""
	@echo "publish"
	@echo "  Publish the dist/cakephp-VERSION.zip to github."
	@echo ""
	@echo "Plugins"
	@echo "  Split each of the public namespaces into separate repos and push the to github."
	@echo ""
	@echo "test"
	@echo "  Run the tests for CakePHP."
	@echo ""
	@echo "All other tasks are not intended to be run directly."


test: install
	vendor/bin/phpunit


# Utility target for checking required parameters
guard-%:
	@if [ "$($*)" = '' ]; then \
		echo "Missing required $* variable."; \
		exit 1; \
	fi;


# Download composer
composer.phar:
	curl -sS https://getcomposer.org/installer | php

# Install dependencies
install: composer.phar
	php composer.phar install



# Version bumping & tagging for CakePHP itself
# Update VERSION.txt to new version.
bump-version: guard-VERSION
	@echo "Update VERSION.txt to $(VERSION)"
	# Work around sed being bad.
	mv VERSION.txt VERSION.old
	cat VERSION.old | sed s'/^[0-9]\.[0-9]\.[0-9].*/$(VERSION)/' > VERSION.txt
	rm VERSION.old
	git add VERSION.txt
	git commit -m "Update version number to $(VERSION)"

# Tag a release
tag-release: guard-VERSION bump-version
	@echo "Tagging $(VERSION)"
	git tag -s $(VERSION) -m "CakePHP $(VERSION)"
	git push $(REMOTE)
	git push $(REMOTE) --tags



# Tasks for tagging the app skeleton and
# creating a zipball of a fully built app skeleton.
.PHONY: clean package

clean:
	rm -rf build
	rm -rf dist

build:
	mkdir -p build

build/app: build
	git clone git@github.com:$(OWNER)/app.git build/app/

build/cakephp: build
	git checkout-index -a -f --prefix=build/cakephp/

dist/cakephp-$(DASH_VERSION).zip: build/app build/cakephp composer.phar
	mkdir -p dist
	@echo "Installing app dependencies with composer"
	# Install deps with composer
	cd build/app && php ../../composer.phar install
	# Copy the current cakephp libs up so we don't have to wait
	# for packagist to refresh.
	rm -rf build/app/vendor/cakephp/cakephp
	cp -r build/cakephp build/app/vendor/cakephp/cakephp
	# Make a zipball of all the files that are not in .git dirs
	# Including .git will make zip balls huge, and the zipball is
	# intended for quick start non-git, non-cli users
	@echo "Building zipball for $(VERSION)"
	cd build/app && find . -not -path '*.git*' | zip ../../dist/cakephp-$(DASH_VERSION).zip -@

# Easier to type alias for zip balls
package: dist/cakephp-$(DASH_VERSION).zip



# Tasks to publish zipballs to github.
.PHONY: publish release

publish: guard-VERSION guard-GITHUB_USER dist/cakephp-$(DASH_VERSION).zip
	@echo "Creating draft release for $(VERSION). prerelease=$(PRERELEASE)"
	curl $(AUTH) -XPOST $(API_HOST)/repos/$(OWNER)/cakephp/releases -d '{ \
		"tag_name": "$(VERSION)", \
		"name": "CakePHP $(VERSION) released", \
		"draft": true, \
		"prerelease": $(PRERELEASE) \
	}' > release.json
	# Extract id out of response json.
	php -r '$$f = file_get_contents("./release.json"); \
		$$d = json_decode($$f, true); \
		file_put_contents("./id.txt", $$d["id"]);'
	@echo "Uploading zip file to github."
	curl $(AUTH) -XPOST \
		$(UPLOAD_HOST)/repos/$(OWNER)/cakephp/releases/`cat ./id.txt`/assets?name=cakephp-$(DASH_VERSION).zip \
		-H "Accept: application/vnd.github.manifold-preview" \
		-H 'Content-Type: application/zip' \
		--data-binary '@dist/cakephp-$(DASH_VERSION).zip'
	# Cleanup files.
	rm release.json
	rm id.txt

# Tasks for publishing separate reporsitories out of each cake namespace
composer-init:
	for d in Acl Blocks Comments Contacts Core Example Extensions FileManager Install Menus Meta Nodes Dashboards Settings Taxonomy Translate Users Wysiwyg ; do \
		p=`basename $$d` ; \
		( cd $$d && composer init \
			--name croogo/`echo $$p | awk '{print tolower($$0) }'` \
			--license MIT \
			--description "Croogo $$d Plugin" \
			--author "Croogo Development Team <team@croogo.org>" \
			--stability dev \
			--require croogo/core=3.0.x-dev \
			-n \
		) ; \
	done

prepare-repo:
	for d in ./* ; do \
		if [ -d $$d ] ; then \
			r=`basename $$d | awk '{ print tolower($$0) }'` ; \
			mkdir ${GITHUB_ROOT}/$$r ; \
			( cd ${GITHUB_ROOT}/$$r && git init --bare ) ; \
		fi ; \
	done && \
	( cd ${GITHUB_ROOT} && \
		rm -rf config/ src/ test tests/ && \
		for d in * ; do \
			( cd $$d && git symbolic-ref HEAD refs/heads/3.0 ) \
		done \
	)

plugin-split:
	git subsplit update
	git subsplit publish "\
		Acl:$(GITHUB_ROOT)/acl \
		Blocks:$(GITHUB_ROOT)/blocks \
		Comments:$(GITHUB_ROOT)/comments \
		Contacts:$(GITHUB_ROOT)/contacts \
		Core:$(GITHUB_ROOT)/core \
		Dashboards:$(GITHUB_ROOT)/dashboards \
		Example:$(GITHUB_ROOT)/example \
		Extensions:$(GITHUB_ROOT)/extensions \
		FileManager:$(GITHUB_ROOT)/filemanager \
		Install:$(GITHUB_ROOT)/install \
		Menus:$(GITHUB_ROOT)/menus \
		Meta:$(GITHUB_ROOT)/meta \
		Nodes:$(GITHUB_ROOT)/nodes \
		Settings:$(GITHUB_ROOT)/settings \
		Taxonomy:$(GITHUB_ROOT)/taxonomy \
		Translate:$(GITHUB_ROOT)/translate \
		Users:$(GITHUB_ROOT)/users \
		Wysiwyg:$(GITHUB_ROOT)/wysiwyg \
		" \
		--no-tags \
		--heads=3.0

plugins: $(foreach plugin, $(PLUGINS), plugin-$(plugin))
plugins-tag: $(foreach plugin, $(PLUGINS), tag-plugin-$(plugin))

plugin-%:
	git checkout $(CURRENT_BRANCH) > /dev/null
	- (git remote add $* git@github.com:$(OWNER)/$*.git -f 2> /dev/null)
	- (git branch -D $* 2> /dev/null)
	git checkout -b $*
	git filter-branch --prune-empty --subdirectory-filter $(shell php -r "echo ucfirst('$*');") -f $*
	git push $* $*:master
	git checkout $(CURRENT_BRANCH) > /dev/null

tag-plugin-%: plugin-% guard-VERSION guard-GITHUB_USER
	@echo "Creating tag for the $* component"
	git checkout $*
	curl $(AUTH) -XPOST $(API_HOST)/repos/$(OWNER)/$*/git/refs -d '{ \
		"ref": "refs\/tags\/$(VERSION)", \
		"sha": "$(shell git rev-parse $*)" \
	}'
	git checkout $(CURRENT_BRANCH) > /dev/null
	git branch -D $*
	git remote rm $*

# Top level alias for doing a release.
release: guard-VERSION guard-GITHUB_USER tag-release package publish PLUGINS-tag
