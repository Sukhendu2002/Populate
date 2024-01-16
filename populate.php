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
	 * Populate posts.
	 *
	 * ## OPTIONS
	 * --count=<number>
	 *     The number of posts to create.
	 *    ---
	 *   default: 5
	 *  ---
	 * --tags=<boolean>
	 *     Whether to add tags or not.
	 *    ---
	 *   default: false
	 *  ---
	 * --category=<boolean>
	 *     Whether to add category or not.
	 *    ---
	 *   default: false
	 *  ---
	 * Example: wp populate post --count=10 --tags=true --category=true
	 *
	 * @since  0.0.1
	 */
	public function post( $args, $assoc_args ): void {
		// Get the count of posts to create.
		$count = 5;
		$istags = false;
		$iscategory = false;
		$isauthor = false;

		if ( isset( $assoc_args['count'] ) ) {
			$count = $assoc_args['count'];
		}
		if ( isset( $assoc_args['tags'] ) ) {
			$istags = filter_var( $assoc_args['tags'], FILTER_VALIDATE_BOOLEAN );
		}
		if ( isset( $assoc_args['category'] ) ) {
			$iscategory = filter_var( $assoc_args['category'], FILTER_VALIDATE_BOOLEAN );
		}
		if ( isset( $assoc_args['author'] ) ) {
			$isauthor = filter_var( $assoc_args['author'], FILTER_VALIDATE_BOOLEAN );
		}

		$tags = [];
		$categories = [];
		$tag_ids = [];
		$category_ids = [];
		$author_ids = [];


		if ( $istags ) {
			$tags = get_terms( array(
				'taxonomy' => 'post_tag',
				'hide_empty' => false,
			) );
			if ( count( $tags ) < 3 ) {
				WP_CLI::line( 'Not Enough Tags, Generating Tags' );
				$this->tag( $args, $assoc_args );
			}
			$tags = get_terms( array(
				'taxonomy' => 'post_tag',
				'hide_empty' => false,
			) );
			foreach ( $tags as $tag ) {
				$tag_ids[] = $tag->term_id;
			}
		}

		if ( $iscategory ) {
			$categories = get_terms( array(
				'taxonomy' => 'category',
				'hide_empty' => false,
			) );
			if ( count( $categories ) < 3 ) {
				WP_CLI::line( 'Not Enough Categories, Generating Categories' );
				$this->category( $args, $assoc_args );
			}
			$categories = get_terms( array(
				'taxonomy' => 'category',
				'hide_empty' => false,
			) );
			foreach ( $categories as $category ) {
				$category_ids[] = $category->term_id;
			}
		}

		if ( $isauthor ) {
			$authors = get_users( array(
				'role' => 'author',
			) );
			if ( count( $authors ) < 3 ) {
				WP_CLI::line( 'Not Enough Authors, Generating Authors' );
				$this->author( $args, $assoc_args );
			}
			$authors = get_users( array(
				'role' => 'author',
			) );
			foreach ( $authors as $author ) {
				$author_ids[] = $author->ID;
			}
		}

		// Progress bar.
		$progress = \WP_CLI\Utils\make_progress_bar( 'Generating posts', $count );

		// Loop through the number of posts to create.
		for( $i = 0; $i < $count; $i++ ) {
			// Create a new post.
			$post_id = wp_insert_post( array(
				'post_title' => $this->faker->sentence,
				'post_content' => $this->faker->paragraphs( 5, true ),
				'post_status' => 'publish',
				'post_author' => $author_ids[ array_rand( $author_ids, 1 ) ],
				'post_type' => 'post',
				'post_date' => $this->faker->dateTimeBetween( '-1 year', 'now' )->format( 'Y-m-d H:i:s' ),
			) );

			if ( $istags ) {
				//take random 3 tags
				$random_tags = array_rand( $tag_ids, 3 );
				$tags = [];
				foreach ( $random_tags as $random_tag ) {
					$tags[] = $tag_ids[ $random_tag ];
				}
				wp_set_post_terms( $post_id, $tags, 'post_tag' );

			}

			if ( $iscategory ) {
				//take any one category
				 $random_category = array_rand( $category_ids, 1 );
				 $category = [];
				 $category[] = $category_ids[ $random_category ];
				 wp_set_post_terms( $post_id, $category, 'category' );

			}

			// Increment the progress bar.
			$progress->tick();
		}

		// Complete the progress bar.
		$progress->finish();

	}

	/**
	 * Populate authors.
	 *
	 * ## OPTIONS
	 * --count=<number>
	 *     The number of authors to create.
	 *    ---
	 *   default: 5
	 *  ---
	 * Example: wp populate author --count=10
	 *
	 * @since  0.0.1
	 */
	public function author( $args, $assoc_args ): void {
		// Get the count of authors to create.
		$count = 5;

		if ( isset( $assoc_args['count'] ) ) {
			$count = $assoc_args['count'];
		}

		// Progress bar.
		$progress = \WP_CLI\Utils\make_progress_bar( 'Generating authors', $count );

		//add authors
		for( $i = 0; $i < $count; $i++ ) {

			$user_id = wp_insert_user( array(
				'user_login' => $this->faker->userName,
				'user_pass' => $this->faker->password,
				'user_email' => $this->faker->email,
				'first_name' => $this->faker->firstName,
				'last_name' => $this->faker->lastName,
				'role' => 'author',
			) );

			// Increment the progress bar.
			$progress->tick();
		}

		// Complete the progress bar.
		$progress->finish();

		WP_CLI::success( 'Authors created successfully.' );
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
	public function category( $args, $assoc_args ): void {
		// Get the count of categories to create.
		$count = 5;

		if ( isset( $assoc_args['count'] ) ) {
			$count = $assoc_args['count'];
		}

		// Progress bar.
		$progress = \WP_CLI\Utils\make_progress_bar( 'Generating categories', $count );

		// Loop through the number of categories to create.
		for ( $i = 0; $i < $count; $i++ ) {
			// Create a new category.
			wp_insert_term(
				$this->faker->word,
				'category'
			);

			// Increment the progress bar.
			$progress->tick();
		}

		// Complete the progress bar.
		$progress->finish();

		WP_CLI::success( 'Categories created successfully.' );
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
	public function tag( $args, $assoc_args ): void {
		// Get the count of tags to create.
		$count = 5;

		if ( isset( $assoc_args['count'] ) ) {
			$count = $assoc_args['count'];
		}

		// Progress bar.
		$progress = \WP_CLI\Utils\make_progress_bar( 'Generating tags', $count );

		// Loop through the number of tags to create.
		for ( $i = 0; $i < $count; $i++ ) {
			// Create a new tag.
			wp_insert_term(
				$this->faker->word,
				'post_tag'
			);

			// Increment the progress bar.
			$progress->tick();
		}

		// Complete the progress bar.
		$progress->finish();

		WP_CLI::success( 'Tags created successfully.' );
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
