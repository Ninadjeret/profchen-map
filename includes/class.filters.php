<?php

class POGO_filters {
    
    function __construct() {
        add_filter( 'pogo/raid/annonce/embedData', array($this, 'TestCP'), 20, 3 );
    }
    
    function TestCP( $embed, $raid, $connector_id ) {   
        
        if( !$raid->getPokemon() ) return $embed;
        
        $pokemon = $raid->getPokemon(); 
        if( $pokemon->pokedexId == '026' || $pokemon->pokedexId == '103' || $pokemon->pokedexId == '105' ) return $embed;
        
        $message = $embed['embeds'][0]['description']."\r\n";
        $message .= "Météo de base : CP entre {$pokemon->getCp(20, 10, 10, 10)} et {$pokemon->getCp(20, 15, 15, 15)}\r\n";
        $message .= "Boost météo : CP entre {$pokemon->getCp(25, 10, 10, 10)} et {$pokemon->getCp(25, 15, 15, 15)}\r\n";
        $embed['embeds'][0]['description'] = $message;
        return $embed;
    } 
    
}

new POGO_filters();

