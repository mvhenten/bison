<?php
/**
 * Glue/Module.php
 *
 * @author Matthijs van Henten <matthijs@ischen.nl>
 * @package Glue
 */


namespace Glue;

use Glue\CSS;

require_once dirname(__FILE__) . '/CSS.php';

abstract class Module {
    protected $_module     = null;
    protected $_type       = null;
    protected $_element    = null;
    protected $_css        = null;
    protected $_mapping    = null;
    protected $_style      = null;
    protected $_properties = null;
    protected $_content    = null;

    private $_mapping_spec_master = array(
        'height'            => array(
            'name'      => 'object-height',
            'validate'  => '\d+px'
        ),
        'left'              => array(
            'name' => 'object-left',
            'validate' => '\d+px'
        ),
        'top'               => array(
            'name' => 'object-top',
            'validate' => '\d+px'
        ),
        'width'             => array(
            'name' => 'object-width',
            'validate' => '\d+px'
        ),
        'background-color'  => array(
            'name' => 'text-background-color',
            'validate' => '[\w\s-,"]'//'rgba\(\d+, \d+, \d+, \d+\)'
        ),
        'color'             => array(
            'name' => 'text-font-color',
            'validate' => '[\w\s-,"]'//'rgb\(\d+, \d+, \d+\)'
        ),
        'font-family'       => array(
            'name' => 'text-font-family',
            'validate' => '[\w\s-,"]'
        ),
        'font-size'         => array(
            'name' => 'text-font-size',
            'validate' => '\dpx'
        ),
        'font-weight'       => array(
            'name' => 'text-font-weight',
            'validate' => 'bold|normal|'
        ),
        'letter-spacing'    => array(
            'name' => 'text-letter-spacing',
            'validate' => '[\w\s-]'
        ),
        'text-align'        => array(
            'name' => 'text-align',
            'validate' => 'left|right'
        ),
        'height'            => array(
            'name' => 'object-height',
            'validate' => '\d+px'
        ),
        'left'              => array(
            'name' => 'object-left',
            'validate' => '\d+px'
        ),
        'top'               => array(
            'name' => 'object-top',
            'validate' => '\d+px'
        ),
        'width'             => array(
            'name' => 'object-width',
            'validate' => '\d+px'
        )
    );

    /**
     *
     *
     * @param unknown $id      (optional)
     * @param unknown $element
     */
    public function __construct( $id = null, $element ) {
        $this->_id      = $id;
        $this->_element = $element;
    }


    /**
     *
     *
     * @param unknown $name
     * @return unknown
     */
    public function __get( $name ) {
        if ( ($property = "_$name")
            && (isset($this->$property) || property_exists( $this, $property ))) {
            if ( ! isset( $this->$property )
                && method_exists( $this, "_build_$property" ) ) {
                $method = "_build_$property";
                $this->$property = $this->$method();
            }
            return $this->$property;
        }
    }


    /**
     *
     *
     * @return unknown
     */
    public function __toString() {
        $str = join("\n", array( $this->properties, $this->css ) );

        if( $this->content ){
            $str = join("\n", array( $str, '', $this->content) );
        }

        return trim($str);
    }


    /**
     *
     *
     * @return unknown
     */
    protected function _build__css() {
        return new CSS( $this->style, $this->_mapping );
    }

    /**
     *
     *
     * @return unknown
     */
    protected function _build__style() {
        return (array) $this->_element->style;
    }

    protected function style( $name ){
        if( isset($this->style[$name] ) ){
            return $this->style[$name];
        }
    }


    /**
     *
     *
     * @return unknown
     */
    protected function _build__properties() {
        return $this->_flatten(array(
                'type'      => $this->_type,
                'module'    => $this->_module
            ));
    }

    protected function _build__content() {
        if( isset( $this->_element->text ) ){
            return str_replace( "\n", '', $this->_element->text );
        }
    }


    /**
     *
     *
     * @param array   $array
     * @return unknown
     */
    protected function _flatten( array $array ) {
        $collect = array();

        foreach ( $array as $key => $value ) {
            $collect[] = $key . ':' . $value;
        }

        return join( "\n", $collect );
    }


}
