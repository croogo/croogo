BOOTSTRAP_TAG=v2.2.0
FONTAWESOME_TAG=v3.0.2

REPO_FONTAWESOME=git://github.com/FortAwesome/Font-Awesome.git
REPO_BOOTSTRAP=git://github.com/twitter/bootstrap

CROOGO_LESS = ./webroot/less/admin.less
CROOGO_RESPONSIVE_LESS = ./webroot/less/admin-responsive.less

CSS_DIR=$(CURDIR)/webroot/css
JS_DIR=$(CURDIR)/webroot/js
FONT_DIR=$(CURDIR)/webroot/font

CROOGO_CSS=croogo-bootstrap.css
CROOGO_RESPONSIVE_CSS=croogo-bootstrap-responsive.css
BOOTSTRAP_JS=croogo-bootstrap.js

DATE=$(shell date +%I:%M%p)
ifeq ($(RELEASE), true)
	COMPILE=recess --compress
else
	COMPILE=lessc # for debugging as it provides better error messages
endif

CHECK=\033[32m✔\033[39m
HR=\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#

# recess & uglifyjs are required

all: css js assets
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
	@( cd webroot/bootstrap && git checkout -f ${BOOTSTRAP_TAG} > /dev/null 2>&1 )
	@[ "$$?" -eq 0 ] && echo "bootstrap branch/tag: ${BOOTSTRAP_TAG} ${CHECK}"
	@echo "${HR}"
	@echo "Compiling..."
	@${COMPILE} ${CROOGO_LESS} > "${CSS_DIR}"/"${CROOGO_CSS}"
	@DIR=${CSS_DIR} && echo "File: $${DIR#${CURDIR}/}/${CROOGO_CSS} ${CHECK}"
	@${COMPILE} ${CROOGO_RESPONSIVE_LESS} > "${CSS_DIR}"/"${CROOGO_RESPONSIVE_CSS}"
	@DIR=${CSS_DIR} && echo "File: $${DIR#${CURDIR}/}/${CROOGO_RESPONSIVE_CSS} ${CHECK}"

js: webroot/bootstrap
	@( \
	cd webroot/bootstrap ; \
	cat js/bootstrap-transition.js js/bootstrap-alert.js js/bootstrap-button.js js/bootstrap-carousel.js js/bootstrap-collapse.js js/bootstrap-dropdown.js js/bootstrap-modal.js js/bootstrap-tooltip.js js/bootstrap-popover.js js/bootstrap-scrollspy.js js/bootstrap-tab.js js/bootstrap-typeahead.js js/bootstrap-affix.js > "${JS_DIR}"/"${BOOTSTRAP_JS}" \
	)
	@if [ ! -z "${RELEASE}" ]; then \
		uglifyjs --overwrite -nc "${JS_DIR}"/"${BOOTSTRAP_JS}" ;\
	fi
	@DIR=${JS_DIR} && echo "File: $${DIR#${CURDIR}/}/${BOOTSTRAP_JS} ${CHECK}"

assets:
	@echo "${HR}"
	@echo "Copying..."
	@if [ ! -d ${FONT_DIR} ] ; then \
		mkdir "${FONT_DIR}"; \
	fi
	@for file in webroot/fontAwesome/font/* ; do \
		cp $${file} webroot/font/ ; \
		chmod 644 webroot/font/`basename $${file}` ; \
		echo "Copied: webroot/font/`basename $${file}` ${CHECK}" ;\
	done

clean:
	@rm -f "${CSS_DIR}"/"${CROOGO_CSS}" "${CSS_DIR}"/"${CROOGO_RESPONSIVE_CSS}"
	@rm -f "${JS_DIR}"/"${BOOTSTRAP_JS}"
	@rm -rf "${FONT_DIR}"
	@echo "Generated files deleted: ${CHECK}"

.PHONY: bootstrap
