<?php

class POGO_dijon_api {
    
    function __construct() {
        $this->version = 1;
        $this->private_token = '36csw4errmgyju9cznbmj61xbgn659po63pocb443uuept0inp';
        $this->public_token = '36csw4errmgyju9cznbmj61xbgn659po63pocb443uuept0inp';
        add_action( 'rest_api_init', array($this, 'registerRoutes') );
    } 
     
    
    public function registerRoutes() {
        $basename = 'dijon/v'.$this->version.'/';//(?P<parent>\d+)
        
        register_rest_route( $basename, '/raids/imagedecode/', array(
            'methods' => 'GET',
            'callback' => array( $this, 'getRaidDataFromImg' ),
            'args' => array(
                'token' => array(
                    'required' => true,
                    'validate_callback' => function($param, $request, $key) {
                        return $this->isRightToken($param);
                    }
                ),
                'url' => array(
                    'required' => true,
                ),
            ),            
        ) );                    
    }
    
    /**
     * =========================================================================
     * CONTROL METHODS
     * =========================================================================
     */
    
    function isRightToken( $token ) {
        return ( $token == $this->public_token ) ? true : false ;
    }
    
    
    /**
     * =========================================================================
     * RAID METHODS
     * =========================================================================
     */
    
    /**
     * 
     * @param type $request
     * @return type
     */
    function getRaidDataFromImg( $request ) {
        $url = urldecode( $request->get_param('url') );
        $imageAnalyzer = new POGO_IA_engine($url, array(
            'gymDetection' => false
        ));
        $result = $imageAnalyzer->result;
        
        $boss = false ;
        $eggLevel = $result->eggLevel;
        
        if( $result->pokemon ) {
            $boss = $this->preparePokemonForExport($result->pokemon);
        }
        
        $return = (object) array(
            'gym' => $result->gym,
            'eggLevel' => $eggLevel,
            'pokemon'   => $boss,
            'date' => $result->date,
            'error' => $result->error,  
            //'logs' => $result->logs,          
        );
        return rest_ensure_response($return);
    }   
    
    
    /**
     * =========================================================================
     * PREPARE METHODS
     * =========================================================================
     */

    
    public function preparePokemonForExport( $pokemon ) {
        
        if (empty($pokemon)) {
            return false;
        }
        
        $parent = false;
        if( $pokemon->getParent() ) {
            $parent = $this->preparePokemonForExport( $pokemon->getParent() );
        }

        return array(
            'nianticId' => $pokemon->nianticId,
            'pokedexId' => $pokemon->pokedexId,
            'nameFr' => $pokemon->getNameFr(),
            'raidNameFr' => $pokemon->getRaidName(),
            'eggLevel' => $pokemon->getRaidLevel(),
            'cp' => array(
                'lvl20' => array(
                    'min' => $pokemon->getCp(20, 10, 10, 10),
                    'max' => $pokemon->getCp(20, 15, 15, 15),
                ),
                'lvl25' => array(
                    'min' => $pokemon->getCp(25, 10, 10, 10),
                    'max' => $pokemon->getCp(25, 15, 15, 15),
                ),
            ),
            'thumbnailUrl'  => $pokemon->getThumbnailUrl(),
            'parent'        => $parent,
        );
    }   

}

new POGO_dijon_api();
