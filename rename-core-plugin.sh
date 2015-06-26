rm -rf Core
git rm Croogo/tests/bootstrap.php
git mv tests/bootstrap.php Croogo/tests/
git mv Croogo Core
git mv Core/src/Core/Exception Core/src/Exception
git commit -m "Rename Croogo\\Croogo to Croogo\\Core"

files=`find Core/ -name "*.ctp" -o -name "*.php"`
sed -i 's/namespace Croogo\\Croogo;/namespace Croogo\\Core;/' $files
sed -i 's/use Croogo\\Croogo\\/use Croogo\\Core\\/' $files
sed -i 's/use Croogo\\Croogo;/use Croogo\\Core\\Croogo;/' $files
sed -i 's/use Croogo\\Core\\Core/use Croogo\\Core/' $files
git add -u Core
git commit -m "Update usage of renamed namespace Croogo\\Croogo to Croogo\\Core"

make composer-init
sed -i 's/croogo\/croogo/cakephp\/cakephp/' Core/composer.json
sed -i 's/dev-3.0/~3.0/' Core/composer.json
git add */composer.json
git commit -m "Add default composer.json for core plugins"

git apply <<EOF
diff --git a/Core/composer.json b/Core/composer.json
--- a/Core/composer.json
+++ b/Core/composer.json
@@ -2,8 +2,26 @@
     "name": "croogo/core",
     "description": "Croogo Core Plugin",
     "require": {
-        "croogo/core": "3.0.x-dev"
+        "cakephp/cakephp": "~3.0",
+        "croogo/acl": "3.0.x-dev",
+        "croogo/blocks": "3.0.x-dev",
+        "croogo/comments": "3.0.x-dev",
+        "croogo/contacts": "3.0.x-dev",
+        "croogo/extensions": "3.0.x-dev",
+        "croogo/filemanager": "3.0.x-dev",
+        "croogo/install": "3.0.x-dev",
+        "croogo/menus": "3.0.x-dev",
+        "croogo/meta": "3.0.x-dev",
+        "croogo/nodes": "3.0.x-dev",
+        "croogo/settings": "3.0.x-dev",
+        "croogo/taxonomy": "3.0.x-dev",
+        "croogo/translate": "3.0.x-dev",
+        "croogo/users": "3.0.x-dev",
+        "croogo/wysiwyg": "3.0.x-dev"
     },
+    "suggest": [
+        "croogo/example"
+    ],
     "license": "MIT",
     "authors": [
         {
EOF
git add -u Core/composer.json
git commit -m "Complete the 'require' clause in croogo/core/composer.json"

make plugin-split
