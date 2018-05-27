<?php

echo isset($_GET['url']) ? file_get_contents($_GET['url']) : '';

?>