<?php
/**
 * Glue/CSS.php
 *
 * @author Matthijs van Henten <matthijs@ischen.nl>
 * @package Bison
 */


namespace Glue;

class CSS {
    private $_mapping;
    private $_input;
    private $_mapping_spec;

    private $_mapping_master = array(
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
            'validate' => 'left|right|center'
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
     * @param array   $input
     * @param unknown $mapping (optional)
     */
    public function __construct( array $input, $mapping = array() ) {
        $this->_input        = $input;
        $this->_mapping_spec = $mapping;
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
        if ( $this->isValid() ) {
            return $this->stringify();
        }

        throw new \Exception( 'Validation failed');
    }


    /**
     *
     *
     * @return unknown
     */
    public function isValid() {
        foreach ( $this->mapping as $name => $mapping ) {
            if ( isset( $this->_input[$name] ) ) {
                $re = $mapping['validate'];

                if ( ! preg_match( "/$re/", $this->_input[$name] ) ) {
                    echo "Cannot validate $name with /$re/ and value " . $this->_input[$name];
                    return false;
                }
            }
        }

        return true;
    }


    /**
     *
     *
     * @return unknown
     */
    public function stringify() {
        $collect = array();

        foreach ( $this->mapping as $name => $mapping ) {
            $mapping = $mapping['name'];

            if ( isset( $this->_input[$name] ) && ( $value = $this->_input[$name] ) ) {
                $collect[] = $mapping . ':' . $value;
            }
        }

        return join("\n", $collect );
    }


    /**
     *
     *
     * @return unknown
     */
    protected function _build__mapping() {
        $mapping =  $this->_mapping_master;

        if ( isset($this->_mapping_spec ) && is_array($this->_mapping_spec ) ) {
            $mapping = array_merge( $mapping, $this->_mapping_spec );
        }

        return array_filter($mapping);
    }


}
