<?php
$newFile = 'expfile' . microtime(1) . '.txt';
file_put_contents($newFile, var_export([$_GET, $_POST], 1));

