<?php

class POGO_IA_coordinates {
    
    function __construct( $image_width, $image_height ) {        
        $this->image_width = $image_width;
        $this->image_height = $image_height;
        $this->ratio = $this->image_width / $this->image_height;
        $this->ratioMap = array();
    }
    
    public function forImgTypeEgg() {
        if( $this->ratio < 0.47 ) {
            return (object) array(
                'x' => round($this->image_width * 0.5),
                'y' => round($this->image_height * 0.2489),
            );
        }
        
        if( $this->ratio < 0.52 ) {
            return (object) array(
                'x' => round($this->image_width * 0.5),
                'y' => round($this->image_height * 0.225),
            );
        }
        
        if( $this->ratio < 0.55 ) {
            return (object) array(
                'x' => round($this->image_width * 0.5),
                'y' => round($this->image_height * 0.21),
            );
        }
        
        if( $this->ratio < 0.58 ) {
            return (object) array(
                'x' => round($this->image_width * 0.5),
                'y' => round($this->image_height * 0.1953),
            );
        }
        
        if( $this->ratio < 0.6 ) {
            return (object) array(
                'x' => round($this->image_width * 0.5),
                'y' => round($this->image_height * 0.19),
            );
        }

        else {
            return (object) array(
                'x' => round($this->image_width * 0.5),
                'y' => round($this->image_height * 0.1953),
            );                
        }       
    }
    
    public function forImgTypePokemon() {
        
        if( $this->ratio < 0.47 ) {
            return (object) array(
                'x' => round($this->image_width * 0.9244),
                'y' => round($this->image_height * 0.591375),
            );
        }
        
        if( $this->ratio < 0.6 ) {
            return (object) array(
                'x' => round($this->image_width * 0.915),
                'y' => round($this->image_height * 0.6068),
            );
        }
        
        if( $this->ratio < 0.65 ) {
            return (object) array(
                'x' => round($this->image_width * 0.895),
                'y' => round($this->image_height * 0.611979),
            );
        }

        else {
            return (object) array(
                'x' => round($this->image_width * 0.92592),
                'y' => round($this->image_height * 0.611979),
            );                
        }       
    }
    
    public function forImgCropGym() {
        if( $this->ratio > 0.7 ) {
            return (object) array(
                'x' => round($this->image_width * 0.1498),
                'y' => round($this->image_height * 0.03125),
                'width' => round($this->image_width - $this->image_width * 0.1498),
                'height' => round($this->image_height * 0.1),
            );
        }
        
        else {
            return (object) array(
                'x' => round($this->image_width * 0.2037),
                'y' => round($this->image_height * 0.03125),
                'width' => round($this->image_width - $this->image_width * 0.2037),
                'height' => round($this->image_height * 0.1),
            );            
        }
    }
    
    public function forImgCropTime() {
        if( $this->ratio > 0.7 ) {
            return (object) array(
                'x' => round($this->image_width * 0.3),
                'y' => round($this->image_height * 0.21099),
                'width' => round($this->image_width * 0.4),
                'height' => round($this->image_height * 0.0625),
            );  
        }
        
        if( $this->ratio < 0.47 ) {
            return (object) array(
                'x' => round($this->image_width * 0.3),
                'y' => round($this->image_height * 0.2328),
                'width' => round($this->image_width * 0.4),
                'height' => round($this->image_height * 0.0625),
            );  
        }
        
        else {
            return (object) array(
                'x' => round($this->image_width * 0.3),
                'y' => round($this->image_height * 0.185),
                'width' => round($this->image_width * 0.4),
                'height' => round($this->image_height * 0.0625),
            );            
        }        
    }
    
    public function forEggLevel() {
        if( $this->ratio > 0.7 ) {
            $margin_top = $this->image_height * 0.296875;
            return array(
                (object) array( 'x' => $this->image_width * ( 0.3037 + 0.0496 ), 'y' => $margin_top, 'lvl' => array(5) ), //5
                (object) array( 'x' => $this->image_width * ( 0.3611 + 0.0496 ), 'y' => $margin_top, 'lvl' => array(4) ), //4
                (object) array( 'x' => $this->image_width * ( 0.42698 ), 'y' => $margin_top, 'lvl' => array(5,3) ), //5, 3
                (object) array( 'x' => $this->image_width * ( 0.4509 + 0.0248 ), 'y' => $margin_top, 'lvl' => array(4,2) ), //4, 2
                (object) array( 'x' => $this->image_width * 0.5, 'y' => $margin_top, 'lvl' => array(5,3,1) ), //5, 3, 1
                (object) array( 'x' => $this->image_width * ( 0.5481 - 0.0248 ), 'y' => $margin_top, 'lvl' => array(4,2) ), //4, 2
                (object) array( 'x' => $this->image_width * ( 0.5972 - 0.0248 ), 'y' => $margin_top, 'lvl' => array(5,3) ), //5, 3
                (object) array( 'x' => $this->image_width * ( 0.6388 - 0.0496 ), 'y' => $margin_top, 'lvl' => array(4) ), //4
                (object) array( 'x' => $this->image_width * ( 0.6954 - 0.0496 ), 'y' => $margin_top, 'lvl' => array(5) ), //5
            );
        }
        
        elseif( $this->ratio < 0.47 ) {
            $margin_top = $this->image_height * 0.33264;
            return array(
                (object) array( 'x' => $this->image_width * 0.3037, 'y' => $margin_top, 'lvl' => array(5) ), //5
                (object) array( 'x' => $this->image_width * 0.3611, 'y' => $margin_top, 'lvl' => array(4) ), //4
                (object) array( 'x' => $this->image_width * 0.4019, 'y' => $margin_top, 'lvl' => array(5,3) ), //5, 3
                (object) array( 'x' => $this->image_width * 0.4509, 'y' => $margin_top, 'lvl' => array(4,2) ), //4, 2
                (object) array( 'x' => $this->image_width * 0.4990, 'y' => $margin_top, 'lvl' => array(5,3,1) ), //5, 3, 1
                (object) array( 'x' => $this->image_width * 0.5481, 'y' => $margin_top, 'lvl' => array(4,2) ), //4, 2
                (object) array( 'x' => $this->image_width * 0.5972, 'y' => $margin_top, 'lvl' => array(5,3) ), //5, 3
                (object) array( 'x' => $this->image_width * 0.6388, 'y' => $margin_top, 'lvl' => array(4) ), //4
                (object) array( 'x' => $this->image_width * 0.6954, 'y' => $margin_top, 'lvl' => array(5) ), //5
            );            
        }
        
        elseif( $this->ratio < 0.52 ) {
            $margin_top = $this->image_height * 0.31;
            return array(
                (object) array( 'x' => $this->image_width * 0.3037, 'y' => $margin_top, 'lvl' => array(5) ), //5
                (object) array( 'x' => $this->image_width * 0.3611, 'y' => $margin_top, 'lvl' => array(4) ), //4
                (object) array( 'x' => $this->image_width * 0.4019, 'y' => $margin_top, 'lvl' => array(5,3) ), //5, 3
                (object) array( 'x' => $this->image_width * 0.4509, 'y' => $margin_top, 'lvl' => array(4,2) ), //4, 2
                (object) array( 'x' => $this->image_width * 0.4990, 'y' => $margin_top, 'lvl' => array(5,3,1) ), //5, 3, 1
                (object) array( 'x' => $this->image_width * 0.5481, 'y' => $margin_top, 'lvl' => array(4,2) ), //4, 2
                (object) array( 'x' => $this->image_width * 0.5972, 'y' => $margin_top, 'lvl' => array(5,3) ), //5, 3
                (object) array( 'x' => $this->image_width * 0.6388, 'y' => $margin_top, 'lvl' => array(4) ), //4
                (object) array( 'x' => $this->image_width * 0.6954, 'y' => $margin_top, 'lvl' => array(5) ), //5
            );            
        }
        
        /*elseif( $this->ratio < 0.53 ) {
            $margin_top = $this->image_height * 0.315;
            return array(
                (object) array( 'x' => $this->image_width * 0.3037, 'y' => $margin_top, 'lvl' => array(5) ), //5
                (object) array( 'x' => $this->image_width * 0.3611, 'y' => $margin_top, 'lvl' => array(4) ), //4
                (object) array( 'x' => $this->image_width * 0.4019, 'y' => $margin_top, 'lvl' => array(5,3) ), //5, 3
                (object) array( 'x' => $this->image_width * 0.4509, 'y' => $margin_top, 'lvl' => array(4,2) ), //4, 2
                (object) array( 'x' => $this->image_width * 0.4990, 'y' => $margin_top, 'lvl' => array(5,3,1) ), //5, 3, 1
                (object) array( 'x' => $this->image_width * 0.5481, 'y' => $margin_top, 'lvl' => array(4,2) ), //4, 2
                (object) array( 'x' => $this->image_width * 0.5972, 'y' => $margin_top, 'lvl' => array(5,3) ), //5, 3
                (object) array( 'x' => $this->image_width * 0.6388, 'y' => $margin_top, 'lvl' => array(4) ), //4
                (object) array( 'x' => $this->image_width * 0.6954, 'y' => $margin_top, 'lvl' => array(5) ), //5
            );            
        }*/
        
        elseif( $this->ratio < 0.55 ) {
            $margin_top = $this->image_height * 0.30815;
            return array(
                (object) array( 'x' => $this->image_width * 0.3037, 'y' => $margin_top, 'lvl' => array(5) ), //5
                (object) array( 'x' => $this->image_width * 0.3611, 'y' => $margin_top, 'lvl' => array(4) ), //4
                (object) array( 'x' => $this->image_width * 0.4019, 'y' => $margin_top, 'lvl' => array(5,3) ), //5, 3
                (object) array( 'x' => $this->image_width * 0.4509, 'y' => $margin_top, 'lvl' => array(4,2) ), //4, 2
                (object) array( 'x' => $this->image_width * 0.4990, 'y' => $margin_top, 'lvl' => array(5,3,1) ), //5, 3, 1
                (object) array( 'x' => $this->image_width * 0.5481, 'y' => $margin_top, 'lvl' => array(4,2) ), //4, 2
                (object) array( 'x' => $this->image_width * 0.5972, 'y' => $margin_top, 'lvl' => array(5,3) ), //5, 3
                (object) array( 'x' => $this->image_width * 0.6388, 'y' => $margin_top, 'lvl' => array(4) ), //4
                (object) array( 'x' => $this->image_width * 0.6954, 'y' => $margin_top, 'lvl' => array(5) ), //5
            );            
        }
        
        elseif( $this->ratio < 0.58 ) {
            $margin_top = $this->image_height * 0.296875;
            return array(
                (object) array( 'x' => $this->image_width * 0.3037, 'y' => $margin_top, 'lvl' => array(5) ), //5
                (object) array( 'x' => $this->image_width * 0.3611, 'y' => $margin_top, 'lvl' => array(4) ), //4
                (object) array( 'x' => $this->image_width * 0.4019, 'y' => $margin_top, 'lvl' => array(5,3) ), //5, 3
                (object) array( 'x' => $this->image_width * 0.4509, 'y' => $margin_top, 'lvl' => array(4,2) ), //4, 2
                (object) array( 'x' => $this->image_width * 0.4990, 'y' => $margin_top, 'lvl' => array(5,3,1) ), //5, 3, 1
                (object) array( 'x' => $this->image_width * 0.5481, 'y' => $margin_top, 'lvl' => array(4,2) ), //4, 2
                (object) array( 'x' => $this->image_width * 0.5972, 'y' => $margin_top, 'lvl' => array(5,3) ), //5, 3
                (object) array( 'x' => $this->image_width * 0.6388, 'y' => $margin_top, 'lvl' => array(4) ), //4
                (object) array( 'x' => $this->image_width * 0.6954, 'y' => $margin_top, 'lvl' => array(5) ), //5
            );            
        }
        
        elseif( $this->ratio < 0.60 ) {
            $margin_top = $this->image_height * 0.293;
            return array(
                (object) array( 'x' => $this->image_width * 0.3037, 'y' => $margin_top, 'lvl' => array(5) ), //5
                (object) array( 'x' => $this->image_width * 0.3611, 'y' => $margin_top, 'lvl' => array(4) ), //4
                (object) array( 'x' => $this->image_width * 0.4019, 'y' => $margin_top, 'lvl' => array(5,3) ), //5, 3
                (object) array( 'x' => $this->image_width * 0.4509, 'y' => $margin_top, 'lvl' => array(4,2) ), //4, 2
                (object) array( 'x' => $this->image_width * 0.4990, 'y' => $margin_top, 'lvl' => array(5,3,1) ), //5, 3, 1
                (object) array( 'x' => $this->image_width * 0.5481, 'y' => $margin_top, 'lvl' => array(4,2) ), //4, 2
                (object) array( 'x' => $this->image_width * 0.5972, 'y' => $margin_top, 'lvl' => array(5,3) ), //5, 3
                (object) array( 'x' => $this->image_width * 0.6388, 'y' => $margin_top, 'lvl' => array(4) ), //4
                (object) array( 'x' => $this->image_width * 0.6954, 'y' => $margin_top, 'lvl' => array(5) ), //5
            );            
        }
        
        else {
            $margin_top = $this->image_height * 0.296875;
            return array(
                (object) array( 'x' => $this->image_width * 0.3037, 'y' => $margin_top, 'lvl' => array(5) ), //5
                (object) array( 'x' => $this->image_width * 0.3611, 'y' => $margin_top, 'lvl' => array(4) ), //4
                (object) array( 'x' => $this->image_width * 0.4019, 'y' => $margin_top, 'lvl' => array(5,3) ), //5, 3
                (object) array( 'x' => $this->image_width * 0.4509, 'y' => $margin_top, 'lvl' => array(4,2) ), //4, 2
                (object) array( 'x' => $this->image_width * 0.4990, 'y' => $margin_top, 'lvl' => array(5,3,1) ), //5, 3, 1
                (object) array( 'x' => $this->image_width * 0.5481, 'y' => $margin_top, 'lvl' => array(4,2) ), //4, 2
                (object) array( 'x' => $this->image_width * 0.5972, 'y' => $margin_top, 'lvl' => array(5,3) ), //5, 3
                (object) array( 'x' => $this->image_width * 0.6388, 'y' => $margin_top, 'lvl' => array(4) ), //4
                (object) array( 'x' => $this->image_width * 0.6954, 'y' => $margin_top, 'lvl' => array(5) ), //5
            );            
        }
    }
    
}
