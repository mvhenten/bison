<?php
/**
 * Glue/Create.php
 *
 * @author Matthijs van Henten <matthijs@ischen.nl>
 * @package Bison
 */


namespace Glue;

require_once dirname(__FILE__) . '/Util.php';

use Glue\Util;
use Glue\Util\Base;

if ( !defined('BISON_SUPPORT_DIR' ) ) {
    define('BISON_SUPPORT_DIR', dirname( __FILE__ ) . '/../support/' );
}

class Create extends Base {

    private $_blacklist = array(
        '.',
        '..',
        'index.php',
        'content',
        'htaccess-dist',
        '.htaccess',
        'user-config.inc.php'
    );

    protected $_source_files;
    protected $_source_path;

    /**
     *
     *
     * @param unknown $target
     */
    public function __construct( $target ) {
        $this->_target = $target;
    }


    /**
     *
     */
    public function create() {
        $content_dir = $this->_targetPath( 'content' );

        if ( file_exists( $content_dir ) ) {
            die( "$content_dir exists, bailing out!\n" );
        }

        mkdir( $this->_targetPath( 'content' ) );
        copy( $this->_sourcePath('content/.htaccess'), $this->_targetPath('content/.htaccess') );
        copy( $this->_sourcePath( 'htaccess-dist'), $this->_targetPath( '.htaccess') );

        $this->_symlinkSources();
        $this->_writeConfig();
    }


    /**
     *
     */
    private function _symlinkSources() {
        foreach ( $this->source_files  as $file ) {
            symlink( $this->_sourcePath( $file ), $this->_targetPath( $file ) );
        }
    }


    /**
     *
     */
    private function _writeConfig() {
        $secret = crypt(microtime(true) + rand(0, 99999));

        $s = '<?php'."\n";
        $s .= '@define(\'AUTH_USER\', \''.$secret.'\');'."\n";
        $s .= '@define(\'AUTH_PASSWORD\', \''.$secret.'\');'."\n";
        $s .= '?>'."\n";

        file_put_contents( $this->_targetPath( 'user-config.inc.php' ), $s);
    }


    /**
     *
     *
     * @return unknown
     */
    protected function _build__source_files() {
        $files = array();

        foreach ( scandir( $this->source_path ) as $file ) {
            if ( in_array( $file, $this->_blacklist ) ) {
                continue;
            }
            $files[] = $file;
        }

        return $files;
    }


    /**
     *
     *
     * @return unknown
     */
    protected function _build__source_path() {
        return realpath( BISON_SUPPORT_DIR . '/' . 'hotglue2' );
    }


    /**
     *
     *
     * @param unknown $file
     * @return unknown
     */
    private function _sourcePath( $file ) {
        return $this->source_path . '/' . $file;
    }


    /**
     *
     *
     * @param unknown $file
     * @return unknown
     */
    private function _targetPath( $file ) {
        return  $this->target . '/' . $file;
    }


}
