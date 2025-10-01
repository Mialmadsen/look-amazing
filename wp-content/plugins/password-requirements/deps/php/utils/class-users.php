<?php
/**
 * Users class
 *
 * @package Teydea_Studio\Password_Requirements\Dependencies\Utils
 */

namespace Teydea_Studio\Password_Requirements\Dependencies\Utils;

use WP_Site;

/**
 * The "Users" class
 */
class Users {
	/**
	 * Cache group
	 *
	 * @var string
	 */
	const CACHE_GROUP = 'users';

	/**
	 * Cache key for "roles" storage
	 *
	 * @var string
	 */
	const CACHE_KEY__ROLES = 'roles';

	/**
	 * Container instance
	 *
	 * @var Container
	 */
	protected Container $container;

	/**
	 * Constructor
	 *
	 * @param Container $container Container instance.
	 */
	public function __construct( Container $container ) {
		$this->container = $container;
	}

	/**
	 * Get list of user roles known by a given site or a whole network
	 *
	 * @return array<int,array{value:string,title:string,id:int}> Array of known user roles.
	 */
	public function get_known_user_roles(): array {
		$cache = new Cache( $this->container );

		$cache->set_group( self::CACHE_GROUP );
		$cache->set_key( self::CACHE_KEY__ROLES );

		/** @var false|array<int,array{value:string,title:string,id:int}> $data */
		$data = $cache->read();

		if ( false === $data ) {
			$roles = wp_roles();
			$data  = [];

			foreach ( $roles->role_names as $role => $display_name ) {
				$data[] = [
					'id'    => count( $data ),
					'value' => $role,
					'title' => $display_name,
				];
			}

			if ( true === $this->container->is_network_enabled() ) {
				$current_blog_id = get_current_blog_id();

				/**
				 * Include roles from other sub-sites
				 *
				 * @var WP_Site $site
				 */
				foreach ( get_sites() as $site ) {
					// Skip the current blog as it was already processed.
					if ( Type::ensure_int( $site->blog_id ) === $current_blog_id ) {
						continue;
					}

					switch_to_blog( Type::ensure_int( $site->blog_id ) );

					foreach ( $roles->role_names as $role => $display_name ) {
						// Add unique roles only.
						if ( in_array( $role, array_column( $data, 'value' ), true ) ) {
							continue;
						}

						$data[] = [
							'id'    => count( $data ),
							'value' => $role,
							'title' => $display_name,
						];
					}

					restore_current_blog();
				}
			}

			foreach ( $data as $index => &$role_data ) {
				// Ensure that ID starts with "1".
				$role_data['id']   += 1;
				$role_data['title'] = sprintf(
					'%1$s ("%2$s")',
					$role_data['title'],
					$role_data['value'],
				);
			}

			$cache->write( $data );
		}

		return $data;
	}
}
