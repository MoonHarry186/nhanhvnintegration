<?php

/*
 * Nhanhvn Install
 *
 * Handles the installation and activation of the Nhanhvn plugin.
 *
 * @class Nhanhvn_Install
 * @version 1.0.0
 */
class Nhanhvn_Install {

    // Fired when the plugin is activated
    public static function activate() {
        self::install_db();
    }

    // Fired when the plugin is deactivated
    public static function deactivate() {
        self::remove_db();
    }


    // Install database when the plugin is activated
    private static function install_db() {
        // Access the global $wpdb object for database interactions
		global $wpdb;

		// Check if the table 'pod_settings' already exists in the database
		if ($wpdb->get_var("SHOW TABLES LIKE 'wp_nhanh_tokens'") != 'wp_nhanh_tokens') {

			// Temporary variable to store each line of the SQL query
			$templine = '';

			// Define the path to the SQL file containing the sample data
			$sql_file = NHANHVN_ABSPATH . 'includes' . '/' . 'initial-data.sql';

            

			// Open the SQL file in read mode
			$handle = fopen($sql_file, 'r');

			// Read the entire contents of the file
			$lines = fread($handle, @filesize($sql_file));

			// Split the file contents into an array of lines
			$lines = explode("\n", $lines);

			// Loop through each line of the SQL file
			foreach ($lines as $line) {

				// Get the first two characters of the line
				$s1 = substr($line, 0, 2);

				// Check if the line is not a comment and is not empty
				if ($s1 != '--' && $line !== '') {

					// Concatenate the line to the current SQL statement
					$templine .= $line;

					// Trim whitespace from the end of the line
					$line = trim($line);

					// Get the last character of the line
					$s2 = substr($line, -1, 1);

					// Check if the line ends with a semicolon, indicating the end of a SQL statement
					if ($s2 == ';') {
						// The complete SQL statement is ready

						// Store the full query
						$sql = $templine;

						// Execute the SQL statement using $wpdb->query()
						// The second parameter 'false' is deprecated and unnecessary
						$wpdb->query($sql);

						// Reset $templine for the next SQL statement
						$templine = '';
					}
				}
			}

			fclose($handle);
		}
    }

    private static function remove_db() {
        global $wpdb;
        $wpdb->query("DROP TABLE IF EXISTS `wp_nhanh_tokens`");
    
    }
}
