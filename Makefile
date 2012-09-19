BOOTSTRAP_TAG=v2.2.0
FONTAWESOME_TAG=master

REPO_FONTAWESOME=git://github.com/FortAwesome/Font-Awesome.git
REPO_BOOTSTRAP=git://github.com/twitter/bootstrap

CROOGO_LESS = ./webroot/less/admin.less
CROOGO_RESPONSIVE_LESS = ./webroot/less/admin-responsive.less

CSS_DIR=webroot/css
JS_DIR=webroot/js
FONT_DIR=webroot/font

CROOGO_CSS=croogo-bootstrap.css
CROOGO_RESPONSIVE_CSS=croogo-bootstrap-responsive.css
BOOTSTRAP_JS=croogo-bootstrap.js

DATE=$(shell date +%I:%M%p)
COMPILE=recess --compile
COMPILE=lessc # we can use lessc since sometimes it gives better error messages

CHECK=\033[32m✔\033[39m
HR=\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#

# recess & uglifyjs are required

all: css js assets
	@echo "Done."

webroot/fontAwesome:
	git clone ${REPO_FONTAWESOME} webroot/fontAwesome

webroot/bootstrap:
	git clone ${REPO_BOOTSTRAP} webroot/bootstrap

deps: webroot/fontAwesome webroot/bootstrap

css: deps
	@echo "${HR}"
	@echo "Setup dependencies..."
	@( cd webroot/fontAwesome && git checkout -f ${FONTAWESOME_TAG} > /dev/null 2>&1 )
	@[ "$$?" -eq 0 ] && echo "fontAwesome branch/tag: ${FONTAWESOME_TAG} ${CHECK}"
	@( cd webroot/bootstrap && git checkout -f ${BOOTSTRAP_TAG} > /dev/null 2>&1 )
	@[ "$$?" -eq 0 ] && echo "bootstrap branch/tag: ${BOOTSTRAP_TAG} ${CHECK}"
	@echo "${HR}"
	@echo "Compiling..."
	@${COMPILE} ${CROOGO_LESS} > ${CSS_DIR}/${CROOGO_CSS}
	@echo "File: ${CSS_DIR}/${CROOGO_CSS} ${CHECK}"
	@${COMPILE} ${CROOGO_RESPONSIVE_LESS} > ${CSS_DIR}/${CROOGO_RESPONSIVE_CSS}
	@echo "File: ${CSS_DIR}/${CROOGO_RESPONSIVE_CSS} ${CHECK}"

js: webroot/bootstrap
	@( \
	cd webroot/bootstrap ; \
	cat js/bootstrap-transition.js js/bootstrap-alert.js js/bootstrap-button.js js/bootstrap-carousel.js js/bootstrap-collapse.js js/bootstrap-dropdown.js js/bootstrap-modal.js js/bootstrap-tooltip.js js/bootstrap-popover.js js/bootstrap-scrollspy.js js/bootstrap-tab.js js/bootstrap-typeahead.js js/bootstrap-affix.js > ../js/${BOOTSTRAP_JS} \
	)
	@echo "File: ${JS_DIR}/${BOOTSTRAP_JS} ${CHECK}"

assets:
	@echo "${HR}"
	@echo "Copying..."
	@if [ ! -d ${FONT_DIR} ] ; then \
		mkdir ${FONT_DIR}; \
	fi
	@for file in webroot/fontAwesome/font/* ; do \
		cp $${file} webroot/font/ ; \
		echo "Copied: webroot/fontAwesome/font/`basename $${file}` ${CHECK}" ;\
	done

clean:
	@rm -f ${CSS_DIR}/${CROOGO_CSS} ${CSS_DIR}/${CROOGO_RESPONSIVE_CSS}
	@rm -f ${JS_DIR}/${BOOTSTRAP_JS}
	@rm -rf ${FONT_DIR}
	@echo "Generated files deleted: ${CHECK}"

.PHONY: bootstrap
