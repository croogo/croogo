BOOTSTRAP_TAG=v4.0.0-beta.2
FONTAWESOME_TAG=v4.7.0

REPO_FONTAWESOME=git://github.com/FortAwesome/Font-Awesome.git
REPO_BOOTSTRAP=git://github.com/twbs/bootstrap

CROOGO_SASS = ./webroot/scss/croogo-admin.scss

CSS_DIR=$(CURDIR)/webroot/css/core
JS_DIR=$(CURDIR)/webroot/js/core
FONT_DIR=$(CURDIR)/webroot/font

CROOGO_CSS=croogo-admin.css
BOOTSTRAP_JS=admin.js

DATE=$(shell date +%I:%M%p)
ifeq ($(RELEASE), true)
	COMPILE=node-sass --output-style compressed
else
	COMPILE=node-sass
endif

CHECK=\033[32m✔\033[39m
HR=\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#

all: css assets
	@echo "Done."

webroot/fontAwesome:
	git clone -b ${FONTAWESOME_TAG} ${REPO_FONTAWESOME} webroot/fontAwesome

webroot/bootstrap:
	git clone -b ${BOOTSTRAP_TAG} ${REPO_BOOTSTRAP} webroot/bootstrap

deps: webroot/fontAwesome webroot/bootstrap

css: deps
	@echo "${HR}"
	@echo "Setup dependencies..."
	@( cd webroot/fontAwesome && git checkout -f ${FONTAWESOME_TAG} > /dev/null 2>&1 )
	@[ "$$?" -eq 0 ] && echo "fontAwesome branch/tag: ${FONTAWESOME_TAG} ${CHECK}"
	@( cd webroot/bootstrap && git fetch --all && git checkout -f ${BOOTSTRAP_TAG} > /dev/null 2>&1 )
	@[ "$$?" -eq 0 ] && echo "bootstrap branch/tag: ${BOOTSTRAP_TAG} ${CHECK}"
	@echo "${HR}"
	@echo "Compiling..."
	@${COMPILE} ${CROOGO_SASS} > "${CSS_DIR}"/"${CROOGO_CSS}"
	@DIR=${CSS_DIR} && echo "File: $${DIR#${CURDIR}/}/${CROOGO_CSS} ${CHECK}"

assets:
	@echo "${HR}"
	@echo "Copying..."
	@if [ ! -d ${FONT_DIR} ] ; then \
		mkdir "${FONT_DIR}"; \
	fi
	@for file in webroot/fontAwesome/fonts/* ; do \
		cp $${file} webroot/fonts/ ; \
		chmod 644 webroot/fonts/`basename $${file}` ; \
		echo "Copied: webroot/fonts/`basename $${file}` ${CHECK}" ;\
	done
	@cp webroot/bootstrap/dist/js/bootstrap.min.js ${JS_DIR}/bootstrap.min.js
	@echo "Copied: webroot/js/core/bootstrap.min.js ${CHECK}"

clean:
	@rm -f "${CSS_DIR}"/"${CROOGO_CSS}" "${CSS_DIR}"/"${CROOGO_RESPONSIVE_CSS}"
	@rm -rf "${FONT_DIR}"
	@echo "Generated files deleted: ${CHECK}"

.PHONY: bootstrap
