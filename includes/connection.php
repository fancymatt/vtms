<?php
	define("DATABASE_HOST", "hmvtms.db.5770926.hostedresource.com");
	define("DATABASE_USERNAME", "hmvtms");
	define("DATABASE_PASSWORD", "ketchup100%OK");
	define("DATABASE_NAME", "hmvtms");
	
	mysql_connect(DATABASE_HOST, DATABASE_USERNAME, DATABASE_PASSWORD)
		or die("<p>Error connecting to database: " . mysql_error() . "</p>");

	mysql_select_db(DATABASE_NAME)
		or die("<p>Error selecting the database: " . DATABASE_NAME . mysql_error() . "</p>");
?>