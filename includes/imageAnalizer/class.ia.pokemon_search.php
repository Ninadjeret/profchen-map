<?php

class POGO_IA_pokemonSearch {
    
    /**
     * 
     */
    function __construct() {
        $this->debug = false;
        $this->query = false;
        $this->pokemons = POGO_helpers::getRaidBosses();
        $this->sanitizedNames = $this->getSanitizedNames();
    }
    
    
    /**
     * 
     * @return type
     */
    function getSanitizedNames() {
        $names = array();
        foreach( $this->pokemons as $pokemon_id ) { 
            $pokemon = new POGO_pokemon($pokemon_id);
            $names[] = sanitize_title($pokemon->getRaidName());            
        }
        return $names;
    } 
    
    
    /**
     * 
     * @return type
     */
    function getAllIdentifiers() {
        
        $identifiers = array();
        
        foreach( $this->pokemons as $pokemon_id ) {
            $pokemon = new POGO_pokemon($pokemon_id);
            $name = sanitize_title($pokemon->getRaidName());
            $nb_chars = strlen($name);
            
            //Parcours
            $debut = 0;
            while( $debut < $nb_chars - 1) {
                
                $fin = $nb_chars - $debut;
                while( $fin > 2 ) {
                    $pattern = mb_strimwidth($name, $debut, $fin);
                    //echo $pattern.'<br>';
                    $is_find = 0;
                    foreach( $this->sanitizedNames as $sanitizedName ) {
                        if( strstr($sanitizedName, $pattern) ) { 
                            $is_find++;
                        } 
                    }
                    
                    if( $is_find === 1 ) {
                        //echo 'Identifiant OK<br>';
                        $identifiers[$pattern] = (object) array(
                            'pokemonId' => $pokemon->wpId,
                            'percent' => round( strlen($pattern) * 100 / $nb_chars )
                        );
                    }
               
                    $fin--;
                }
                
                $debut++;
            }
            //die();
        } 
        $keys = array_map('strlen', array_keys($identifiers));
        array_multisort($keys, SORT_DESC, $identifiers);
        return $identifiers;
    }
    
    
    /**
     * 
     * @param type $query
     * @param type $min
     * @return boolean|\POGO_gym
     */
    function findPokemon( $query, $min = 50 ) {
        $this->query = $query;
        $sanitizedQuery = sanitize_title($this->query);
        
        foreach( $this->getAllIdentifiers() as $pattern => $data ) {
            if( strstr($sanitizedQuery, $pattern) && $data->percent >= $min ) {
                //var_dump($pattern);
                return new POGO_pokemon($data->pokemonId);
            }
        }
        return false;
    }
    
    public function findPokemonFromFragments( $start, $end ) {
        
        $sanitized_start = sanitize_title($start);
        $sanitized_end = sanitize_title($end);
        
        foreach( $this->pokemons as $pokemon_id ) {
            $pokemon = new POGO_pokemon($pokemon_id);
            $sanitized_name = sanitize_title($pokemon->getRaidName());
            if( preg_match('/^'.$sanitized_start.'/i', $sanitized_name) && preg_match('/'.$sanitized_end.'$/i', $sanitized_name) ) {
                return $pokemon;
            }
        }
        
        return false;
    }
    
}

