BOOTSTRAP_TAG=v3.3.1
FONTAWESOME_TAG=v4.2.0

REPO_FONTAWESOME=git://github.com/FortAwesome/Font-Awesome.git
REPO_BOOTSTRAP=git://github.com/twbs/bootstrap

CROOGO_LESS = ./webroot/less/admin.less

CSS_DIR=$(CURDIR)/webroot/css
JS_DIR=$(CURDIR)/webroot/js
FONT_DIR=$(CURDIR)/webroot/fonts

CROOGO_CSS=admin.min.css

DATE=$(shell date +%I:%M%p)
ifeq ($(RELEASE), true)
	COMPILE=node_modules/.bin/lessc --compress
else
	COMPILE=node_modules/.bin/lessc
endif

CHECK=\033[32m✔\033[39m
HR=\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#\#

# recess & uglifyjs are required

all: css js assets
	@echo "Done."

node_modules/.bin/lessc:
	npm install

webroot/fontAwesome:
	git clone -b ${FONTAWESOME_TAG} ${REPO_FONTAWESOME} webroot/fontAwesome

webroot/bootstrap:
	git clone -b ${BOOTSTRAP_TAG} ${REPO_BOOTSTRAP} webroot/bootstrap

webroot/bootstrap/node_modules/.bin:
	(cd webroot/bootstrap && npm install)

webroot/bootstrap/dist/js/bootstrap.min.js:
	@echo "Building bootstrap..."
	@( cd webroot/bootstrap && grunt dist )

deps: webroot/fontAwesome webroot/bootstrap \
	webroot/bootstrap/node_modules/.bin webroot/bootstrap/dist/js/bootstrap.js \
	node_modules/.bin/lessc

css: deps
	@echo "${HR}"
	@echo "Setup dependencies..."
	@( cd webroot/fontAwesome && git checkout -f ${FONTAWESOME_TAG} > /dev/null 2>&1 )
	@[ "$$?" -eq 0 ] && echo "fontAwesome branch/tag: ${FONTAWESOME_TAG} ${CHECK}"
	@( cd webroot/bootstrap && git checkout -f ${BOOTSTRAP_TAG} > /dev/null 2>&1 )
	@[ "$$?" -eq 0 ] && echo "bootstrap branch/tag: ${BOOTSTRAP_TAG} ${CHECK}"
	@echo "${HR}"
	@echo "Compiling less files..."
	@${COMPILE} ${CROOGO_LESS} > "${CSS_DIR}"/"${CROOGO_CSS}"
	@DIR=${CSS_DIR} && echo "File: $${DIR#${CURDIR}/}/${CROOGO_CSS} ${CHECK}"

js: deps
	@for file in webroot/bootstrap/dist/js/bootstrap*.js ; do \
		cp $${file} webroot/js/ ; \
		chmod 644 webroot/js/`basename $${file}` ; \
		echo "Copied: webroot/js/`basename $${file}` ${CHECK}" ;\
	done

assets: deps
	@echo "${HR}"
	@echo "Copying..."
	@if [ ! -d ${FONT_DIR} ] ; then \
		mkdir "${FONT_DIR}"; \
	fi
	@for file in webroot/fontAwesome/fonts/* ; do \
		cp $${file} ${FONT_DIR} ; \
		chmod 644 ${FONT_DIR}/`basename $${file}` ; \
		echo "Copied: ${FONT_DIR}/`basename $${file}` ${CHECK}" ;\
	done

clean:
	@rm -f "${CSS_DIR}"/"${CROOGO_CSS}"
	@rm -rf "${FONT_DIR}"
	@echo "Generated files deleted: ${CHECK}"
