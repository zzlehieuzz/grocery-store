<?php
$output = shell_exec('cd ..; php app/console cache:clear');
echo "<pre>$output</pre>";
