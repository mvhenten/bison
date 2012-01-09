<?php

namespace Glue;

class CSS {
    private $_mapping;
    private $_input;

    public function __construct( array $input, array $mapping ){
        $this->_input   = $input;
        $this->_mapping = $mapping;
    }

    public function __toString(){
        if( $this->isValid() ){
            return $this->stringify();
        }

        throw new \Exception( 'Validation failed');
    }

    public function isValid(){
        foreach( $this->_mapping as $name => $mapping ){
            if( isset( $this->_input[$name] ) ){
                $re = $mapping['validate'];

                if( ! preg_match( "/$re/", $this->_input[$name] ) ){
                    echo "Cannot validate $name with /$re/ and value " . $this->_input[$name];
                    return false;
                }
            }
        }

        return true;
    }

    public function stringify(){
        $collect = array();

        foreach( $this->_mapping as $name => $mapping ){
            $mapping = $mapping['name'];

            if( isset( $this->_input[$name] ) && ( $value = $this->_input[$name] ) ){
                $collect[] = $mapping . ':' . $value;
            }
        }

        return join("\n", $collect );
    }
}
