<?php
header('Pragma: no-cache');
header('Cache-Control: no-store, no-cache, max-age=0, must-revalidate');
header('Content-Type: text/x-json');
header('X-JSON: ' . $this->fetch('content'));

echo $this->fetch('content');