<?php
/**
 * Populate
 *
 * @package           Populate
 * @author            Sukhendu Sekhar Guria
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       Populate
 * Plugin URI:        https://github.com/Sukhendu2002/Populate
 * Description:       This is a plugin to populate the database with dummy data using wp-cli.
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Sukhendu Sekhar Guria
 * Author URI:        https://github.com/Sukhendu2002
 * Text Domain:       populate
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

use Faker\Factory;

/**
 * Main Populate Class.
 */
class Populate_CLI {

	/**
	 * Faker object.
	 *
	 * @var \Faker\Generator|Factory
	 */
	private \Faker\Generator|Factory $faker;
	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	function __construct() {
		require_once __DIR__ . '/vendor/autoload.php';
		$this->faker = Faker\Factory::create();
	}

	/**
	 * Populate categories.
	 *
	 * ## OPTIONS
	 * --count=<number>
	 *     The number of posts to create.
	 *    ---
	 *   default: 5
	 *  ---
	 * Example: wp populate category --count=10
	 *
	 * @since  0.0.1
	 */
	public function category($args, $assoc_args): void {
		//Get the count of categories to create.
		$count = 5;

		if ( isset( $assoc_args['count'] ) ) {
			$count = $assoc_args['count'];
		}

		//Progress bar.
		$progress = \WP_CLI\Utils\make_progress_bar( 'Generating categories', $count );

		//Loop through the number of categories to create.
		for ($i=0; $i < $count; $i++) {
			//Create a new category.
			wp_insert_term(
				$this->faker->word,
				'category'
			);

			//Increment the progress bar.
			$progress->tick();
		}

		//Complete the progress bar.
		$progress->finish();
	}

	/**
	 * Populate tags.
	 *
	 * ## OPTIONS
	 * --count=<number>
	 *     The number of tags to create.
	 *    ---
	 *   default: 5
	 *  ---
	 * Example: wp populate tag --count=10
	 *
	 * @since  0.0.1
	 */
	public function tag($args, $assoc_args): void {
		//Get the count of tags to create.
		$count = 5;

		if ( isset( $assoc_args['count'] ) ) {
			$count = $assoc_args['count'];
		}

		//Progress bar.
		$progress = \WP_CLI\Utils\make_progress_bar( 'Generating tags', $count );

		//Loop through the number of tags to create.
		for ($i=0; $i < $count; $i++) {
			//Create a new tag.
			wp_insert_term(
				$this->faker->word,
				'post_tag'
			);

			//Increment the progress bar.
			$progress->tick();
		}

		//Complete the progress bar.
		$progress->finish();
	}
}

/**
 * Registers our command when cli gets initialized.
 *
 * @since  1.0.0
 * @author Scott Anderson
 */
function wds_cli_register_commands(): void {
	WP_CLI::add_command( 'populate', 'Populate_CLI' );
}

add_action( 'cli_init', 'wds_cli_register_commands' );
