<?php

class POGO_IA_colorPicker {
    
    function __construct() {
        $this->debug = true;
    }
    
    private function _log( $text ) {
        if( is_array( $text ) ) {
            error_log( print_r($text, true) );
        } else {
            error_log( $text );
        }
    }
    
    public function pickColor( $image, $x, $y ) {
        $rgb = imagecolorsforindex($image, imagecolorat($image, $x, $y ));
        if( $this->debug ) $this->_log('Test pixel at x:'.$x.' & y:'.$y);
        if( $this->debug ) $this->_log('Result : R:'.$rgb['red'].' G:'.$rgb['green'].' B:'.$rgb['blue']); 
        return $rgb;
    }
    
    public function isFutureTimerColor( $rgb ) {
        if(
            ( $rgb['red'] >= 230 && $rgb['red'] <= 255 ) && 
            ( $rgb['green'] >= 130 && $rgb['green'] <= 145 ) && 
            ( $rgb['blue'] >= 144 && $rgb['blue'] <= 154 )         
        ) {
            return true;
        }
        return false;
    }
    
    public function isActiveTimerColor( $rgb ) {
        if(
            ( $rgb['red'] >= 250 && $rgb['red'] <= 256 ) &&
            ( $rgb['green'] >= 110 && $rgb['green'] <= 124 ) && 
            ( $rgb['blue'] >= 52 && $rgb['blue'] <= 70 )        
        ) {
            return true;
        }
        
        if(
            ( $rgb['red'] >= 230 && $rgb['red'] <= 255) &&
            ( $rgb['green'] >= 120 && $rgb['green'] <= 135 ) && 
            ( $rgb['blue'] >= 48 && $rgb['blue'] <= 77 )        
        ) {
            return true;
        }
        
        return false;
    }
    
    public function isEgglevelColor( $rgb ) {
        if( $rgb['red'] > 233 && $rgb['green'] > 233 && $rgb['blue'] > 233 ) {
            return true;
        }
        return false;         
    }
    
}
