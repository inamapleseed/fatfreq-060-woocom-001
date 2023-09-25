<?php

/**
 * Utility class for sesion utilities
 *
 * THIS CLASS SHOULD NEVER BE INSTANTIATED
 */
class RFQTK_WP_Session_Utils {
	/**
	 * Count the total sessions in the database.
	 *
	 * @global wpdb $wpdb
	 *
	 * @return int
	 */
	public static function count_sessions() {
		global $wpdb;

		$query = "SELECT COUNT(*) FROM $wpdb->options WHERE option_name LIKE '_rfqtk_wp_session_expires_%'";

		/**
		 * Filter the query in case tables are non-standard.
		 *
		 * @param string $query Database count query
		 */
		$query = apply_filters( '_rfqtk_wp_session_count_query', $query );

		$sessions = $wpdb->get_var( $query );

		return absint( $sessions );
	}

	/**
	 * Create a new, random session in the database.
	 *
	 * @param null|string $date
	 */
	public static function create_dummy_session( $date = null ) {
		// Generate our date
		if ( null !== $date ) {
			$time = strtotime( $date );

			if ( false === $time ) {
				$date = null;
			} else {
				$expires = date( 'U', strtotime( $date ) );
			}
		}

		// If null was passed, or if the string parsing failed, fall back on a default
		if ( null === $date ) {

			$expires = time() + (int) apply_filters( '_rfqtk_wp_session_expiration', RFQTK_WP_SESSION_EXPIRATION );
		}

		$session_id = self::generate_id();

		// Store the session
		add_option( "_rfqtk_wp_session_{$session_id}", array(), '', 'no' );
		add_option( "_rfqtk_wp_session_expires_{$session_id}", $expires, '', 'no' );
	}


    function RFQTK_wp_session_reset()
    {
        if (defined('WP_SETUP_CONFIG')) {
            return;
        }

        if (!defined('WP_INSTALLING')) {
            /**
             * Determine the size of each batch for deletion.
             *
             * @param int
             */

            set_site_transient( 'update_plugins', null );
            // Delete a batch of old sessions
            RFQTK_WP_Session_Utils::delete_all_sessions();
        }


    }
	/**
	 * Delete old sessions from the database.
	 *
	 * @param int $limit Maximum number of sessions to delete.
	 *
	 * @global wpdb $wpdb
	 *
	 * @return int Sessions deleted.
	 */
    public static function delete_old_sessions( $limit = RFQTK_WP_SESSION_CLEAN_LIMIT ) {

        global $wpdb;

        $limit = absint( $limit );

        $limit = apply_filters('delete_old_sessions_filter',$limit);

        $now = time();

        $sql= "SELECT option_value FROM  $wpdb->options WHERE 
                                      option_value <= ". time() ." AND 
                                      option_name LIKE '%_rfqtk_wp_session_expires_%'
                                       order by option_value DESC LIMIT 1";


        $expiration = $wpdb->get_var($sql);
        if ( gpls_empty($expiration ))
        {
            $expiration=-1;
        }



        $count = 0;

        // Delete expired sessions
        if ( !gpls_empty($expiration ))
        {

            $sql = "delete FROM 
        $wpdb->options WHERE ((option_name LIKE '%_rfqtk_wp_session_expires_%'  
                         OR option_name LIKE  CONCAT('_rfqtk_wp_session_', replace(option_name,'_rfqtk_wp_session_expires_', ''))) 
        and option_value <= " . $expiration . ") OR (option_name LIKE '%_rfqtk_wp_session_%' and option_value='a:0:{}')  LIMIT " . $limit . " ";



            $count = (int)$wpdb->get_results($sql);

            return $count;
        }
    }


    /**
	 * Remove all sessions from the database, regardless of expiration.
	 *
	 * @global wpdb $wpdb
	 *
	 * @return int Sessions deleted
	 */
	public static function delete_all_sessions() {
		global $wpdb;

		$count = $wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE '_rfqtk_wp_session_%'" );

		return (int) ( $count );
	}

	/**
	 * Generate a new, random session ID.
	 *
	 * @return string
	 */
	public static function generate_id() {
		require_once( ABSPATH . 'wp-includes/class-phpass.php' );
		$hash = new \PasswordHash( 8, false );
//echo md5( $hash->get_random_bytes( 32 ) );
		return md5( $hash->get_random_bytes( 32 ) );
	}

}