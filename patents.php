#!/usr/bin/php -q
<?php
$date = date("Y-m-d H:i:s");
mail("kmitchner@gmail.com","Look at this week's patents! ({$date})","http://www.uspto.gov/news/og/patent_og/index.jsp");
