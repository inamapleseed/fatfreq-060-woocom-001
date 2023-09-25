<?php
/**
 * WordPress session managment.
 *
 * Standardizes WordPress session data using database-backed options for storage.
 * for storing user session information.
 *
 * @package WordPress
 * @subpackage Session
 * @since   3.7.0
 */


    /**
     * WordPress Session class for managing user session data.
     *
     * @package WordPress
     * @since   3.7.0
     */
    final class RFQTK_WP_Session extends RFQTK_Recursive_ArrayAccess
    {
        /**
         * ID of the current session.
         *
         * @var string
         */
        public $session_id;

        /**
         * Unix timestamp when session expires.
         *
         * @var int
         */
        protected $expires;

        /**
         * Unix timestamp indicating when the expiration time needs to be reset.
         *
         * @var int
         */
        protected $exp_variant;

        /**
         * Singleton instance.
         *
         * @var bool|WP_Session
         */
        private static $instance = false;

        /**
         * Retrieve the current session instance.
         *
         * @param bool $session_id Session ID from which to populate data.
         *
         * @return bool|WP_Session
         */
        public static function get_instance()
        {
            if (!self::$instance) {
                self::$instance = new self();
            }

            return self::$instance;
        }

        /**
         * Default constructor.
         * Will rebuild the session collection from the given session ID if it exists. Otherwise, will
         * create a new session with that ID.
         *
         * @param $session_id
         * @uses apply_filters Calls `wp_session_expiration` to determine how long until sessions expire.
         */
        protected function __construct()
        {

           // error_reporting(0);


     if (isset($_COOKIE[RFQTK_WP_SESSION_COOKIE]))
    {
      //  echo "yes";
        $cookie = stripslashes($_COOKIE[RFQTK_WP_SESSION_COOKIE]);
        $cookie_crumbs = explode('||', $cookie);


        if ($this->is_valid_md5($cookie_crumbs[0])) {
//echo "valid";

            $this->session_id = $cookie_crumbs[0];
            //echo $this->session_id;

        } else {
            //echo "invalid";
            $this->regenerate_id(true);
        }

        $this->expires = absint($cookie_crumbs[1]);
        $this->exp_variant = absint($cookie_crumbs[2]);

        // Update the session expiration if we're past the variant time
        if (time() > $this->exp_variant) {
            $this->set_expiration();
            delete_option("_rfqtk_wp_session_expires_{$this->session_id}");
            add_option("_rfqtk_wp_session_expires_{$this->session_id}", $this->expires, '', 'no');
        }
    } else {
        // echo "no";
        $this->session_id = RFQTK_WP_Session_Utils::generate_id();
         $this->set_cookie();
        $this->set_expiration();
    }


    $this->read_data();
}




        /**
         * Set both the expiration time and the expiration variant.
         * @uses apply_filters Calls `wp_session_expiration_variant` to get the max update window for session data.
         * @uses apply_filters Calls `wp_session_expiration` to get the standard expiration time for sessions.
         */
        protected function set_expiration()
        {
            $this->exp_variant = time() + (int)apply_filters('_rfqtk_wp_session_expiration_variant', RFQTK_WP_SESSION_EXPIRATION_VARIANT );
            $this->expires = time() + (int)apply_filters('_rfqtk_wp_session_expiration', RFQTK_WP_SESSION_EXPIRATION );
        }

        /**
         * Set the session cookie
         * @uses apply_filters Calls `wp_session_cookie_secure` to set the $secure parameter of setcookie()
         * @uses apply_filters Calls `wp_session_cookie_httponly` to set the $httponly parameter of setcookie()
         */
        protected function set_cookie()
        {
           if(!headers_sent()) {
               $secure = apply_filters('_rfqtk_wp_session_cookie_secure', false);
               $httponly = apply_filters('_rfqtk_wp_session_cookie_httponly', false);

               if(!defined("COOKIEPATH")){
                   define("COOKIEPATH" , "");
               }
               if(!defined("COOKIE_DOMAIN")){
                   define('COOKIE_DOMAIN' , "");
               }
               
               setcookie(RFQTK_WP_SESSION_COOKIE, $this->session_id . '||' . $this->expires . '||' . $this->exp_variant, $this->expires, COOKIEPATH, COOKIE_DOMAIN, $secure, $httponly);

           }
        }

        /**
         * Read data from a transient for the current session.
         *
         * Automatically resets the expiration time for the session transient to some time in the future.
         *
         * @return array
         */
        protected function read_data()
        {
            $this->container = get_option("_rfqtk_wp_session_{$this->session_id}", array());

            return $this->container;
        }

        /**
         * Write the data from the current session to the data storage system.
         */
        public function write_data()
        {
            $option_key = "_rfqtk_wp_session_{$this->session_id}";
            if ( $this->dirty ) {

                if (false === get_option($option_key)) {
                    add_option("_rfqtk_wp_session_{$this->session_id}", $this->container, '', 'no');
                    add_option("_rfqtk_wp_session_expires_{$this->session_id}", $this->expires, '', 'no');
                } else {
                    delete_option("_rfqtk_wp_session_{$this->session_id}");
                    add_option("_rfqtk_wp_session_{$this->session_id}", $this->container, '', 'no');
                }
            }

        }

        /**
         * Output the current container contents as a JSON-encoded string.
         *
         * @return string
         */
        public function json_out()
        {
            return json_encode($this->container);
        }

        /**
         * Decodes a JSON string and, if the object is an array, overwrites the session container with its contents.
         *
         * @param string $data
         *
         * @return bool
         */
        public function json_in($data)
        {
            $array = json_decode($data);

            if (is_array($array)) {
                $this->container = $array;
                return true;
            }

            return false;
        }

        /**
         * Regenerate the current session's ID.
         *
         * @param bool $delete_old Flag whether or not to delete the old session data from the server.
         */
        public function regenerate_id($delete_old = false)
        {
            if ($delete_old) {
                delete_option("_rfqtk_wp_session_{$this->session_id}");
            }

            $this->session_id = RFQTK_WP_Session_Utils::generate_id();
           // echo $this->session_id;

            $this->set_cookie();
        }

        /**
         * Check if a session has been initialized.
         *
         * @return bool
         */
        public function session_started()
        {
            return !!self::$instance;
        }

        protected function is_valid_md5( $md5 = '' ){
            return preg_match( '/^[a-f0-9]{32}$/', $md5 );
        }

        /**
         * Return the read-only cache expiration value.
         *
         * @return int
         */
        public function cache_expiration()
        {
            return $this->expires;
        }

        /**
         * Flushes all session variables.
         */
        public function reset()
        {
            $this->container = array();
        }

}