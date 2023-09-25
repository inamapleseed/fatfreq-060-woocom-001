<?php

class Vpc_Upload_Updater {

    protected $version_url = 'https://static.configuratorsuiteforwp.com/vpc-upload-updater.xml';

    public $title = VPC_UPLOAD_NAME;
    protected $auto_updater = false;
    protected $upgrade_manager = false;
    protected $iframe = false;

    public function init() {
        add_filter('upgrader_pre_download', array($this, 'upgradeFilter'), 10, 4);
        add_action('upgrader_process_complete', array($this, 'removeTemporaryDir'));
    }

    /**
     * Setter for manager updater.
     *
     * @param Vpc_Upload_Updating_Manager $updater
     */
    public function setUpdateManager(Vpc_Upload_Updating_Manager $updater) {
        $this->auto_updater = $updater;
    }

    /**
     * Getter for manager updater.
     *
     * @return Vpc_Upload_Updating_Manager
     */
    public function updateManager() {
        return $this->auto_updater;
    }

    /**
     * Get url for version validation
     * @return string
     */
    public function versionUrl() {
        return $this->version_url;
    }

    /**
     * Downloads new version from ORION servers and unzips into temporary directory.
     *
     * @param $reply
     * @param $package
     * @param $updater
     * @return mixed|string|WP_Error
     */
    public function upgradeFilter($reply, $package, $updater) {
        global $wp_filesystem;
        if ( (isset($updater->skin->plugin) && $updater->skin->plugin === VPC_UPLOAD_MAIN_FILE) ||
                (isset($updater->skin->plugin_info) && htmlspecialchars_decode($updater->skin->plugin_info['Name']) === $this->title)
        ) {
            $updater->strings['download_from_servers'] = __('Downloading package from ORION servers...', 'vpc-upload');
            $updater->skin->feedback('download_from_servers');
            $package_filename = 'visual-product-configurator-upload-image-addon.zip';
            $res = $updater->fs_connect(array(WP_CONTENT_DIR));

            if (!$res) {
                return new WP_Error('no_credentials', __("Error! Can't connect to filesystem", 'vpc-upload'));
            }
            $options = get_option('vpc-options');
            if (isset($options['purchase-code-upload-image-add-on']) && !empty($options['purchase-code-upload-image-add-on'])) {
                $license_key = $options['purchase-code-upload-image-add-on'];
            } else {
                return new WP_Error('no_credentials', __('A license key is required to receive automatic updates (<a href=“https://configuratorsuiteforwp.com/guide/how-to-add-my-license/” target=“_blank”>Learn more</a>). Please visit <a href=“https://configuratorsuiteforwp.com/my-account/orders/“ target=“blank”>your account</a> to retrieve it.' . admin_url( 'edit.php?post_type=vpc-config&page=vpc-manage-settings&section=vpc-upload-container' )));
            }

            $args = array('timeout' => 600);
            $site_url = get_site_url();
            $plugin_name = VPC_UPLOAD_NAME;
            $url = "https://configuratorsuiteforwp.com/service/olicenses/v1/checking/?license-key=" . urlencode($license_key) . "&siteurl=" . urlencode($site_url) . "&name=" . urlencode($plugin_name);
            //$url = "https://tests.configuratorsuiteforwp.com/service/olicenses/v1/checking/?license-key=" . urlencode($license_key) . "&siteurl=" . urlencode($site_url) . "&name=" . urlencode($plugin_name);
            $response = wp_remote_get($url, $args);

            if (!is_wp_error($response) ) {
                if (isset($response["body"]) && intval($response["body"]) == 200) {

                    $json = wp_remote_get($this->downloadUrl($this->title), $args);
                    if (is_wp_error($json)) {
                        return $json->get_error_message();
                    }
                    if (isset($json["body"])) {
                        $answer = $json["body"];
                    }

                    $result = array();

                    if (is_array(json_decode($answer, true))) {
                        $result = json_decode($answer, true);
                    } else {
                        return new WP_Error('no_file', __('Error! No file found. Please contact the plugin owners.', 'vpc-upload'));;
                    }

                    if (!isset($result['download_url'])) {
                        return new WP_Error('no_file', __('Error! No file found. Please contact the plugin owners.', 'vpc-upload'));
                    }

                    $download_file = download_url($result['download_url']);
                    if (is_wp_error($download_file)) {
                        return $download_file;
                    }
                    $uploads_dir_obj = wp_upload_dir();
                    $upgrade_folder = $uploads_dir_obj["basedir"] . '/vpc-and-addons/vpc_package';
                    if (!is_dir($upgrade_folder)) {
                        mkdir($upgrade_folder, 0777, true);
                    }
                    //We rename the tmp file to a zip file
                    $new_zipname = str_replace(".tmp", ".zip", $download_file);
                    rename($download_file, $new_zipname);
                    //The upgrade is in the unique directory inside the upgrade folder
                    $new_version = "$upgrade_folder/$package_filename";
                    $result = copy($new_zipname, $new_version);
                    if ($result && is_file($new_version)) {
                        return $new_version;
                    }
                    return new WP_Error('no_credentials', __('Error on unzipping package', 'vpc-upload'));
                } else {
                    return new WP_Error('network_error', __('Wrong license key provided. Please add the correct one and try again.', 'vpc-upload'));
                }
            } else {
                return $response->get_error_message();
            }
        }
        return $reply;
    }

    public function removeTemporaryDir() {
            global $wp_filesystem;
            if (is_dir($wp_filesystem->wp_content_dir() . 'uploads/vpc-and-addons/vpc_package')) {
                $wp_filesystem->delete($wp_filesystem->wp_content_dir() . 'uploads/vpc-and-addons/vpc_package', true);
            }
    }
    protected function downloadUrl( $title ) {
            $site_url = get_site_url();
            $purchase_code = "";
            $options = get_option('vpc-options');
            if (isset($options['purchase-code-upload-image-add-on']) && $options['purchase-code-upload-image-add-on'] !== "") {
                $purchase_code = $options['purchase-code-upload-image-add-on'];
            }
            return "https://configuratorsuiteforwp.com/service/oupdater/v1/update/?name=" . rawurlencode($title) . "&purchase-code=" . $purchase_code . "&siteurl=" . urlencode($site_url);
            //return "https://tests.configuratorsuiteforwp.com/service/oupdater/v1/update/?name=" . rawurlencode($title) . "&purchase-code=" . $purchase_code . "&siteurl=" . urlencode($site_url);
    }
}
