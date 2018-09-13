<?php

class POGO_pokemon {
    
    /**
     * 
     * @param type $wp_id
     * @param type $pokedex_id
     * @param type $niantic_id
     * @return boolean
     */
    function __construct( $wp_id, $pokedex_id = false, $niantic_id = false ) {
        
        if( get_post_type($wp_id) != 'pokemon' ) {
            return false;
        }
        
        $this->wpId = $wp_id;
        $this->pokedexId = ( $pokedex_id ) ? $pokedex_id : $this->_getPokedexId() ;
        $this->nianticId = ( $niantic_id ) ? $niantic_id : $this->_getNianticId() ;
        $this->variantId = $this->_getVariantId() ;
    }
    
    
    /**
     * 
     * @param type $nianticId
     * @return boolean
     */
    public static function initFromNianticId( $nianticId ) {
        if( !POGO_helpers::getPokemonIdFromNianticId($nianticId) ) {
            return false;
        }
        return new POGO_pokemon( POGO_helpers::getPokemonIdFromNianticId($nianticId), false, $nianticId );
    }
    
    
    /**
     * 
     * @param type $pokedexId
     * @return boolean
     */
    public static function initFromPokedexId( $pokedexId ) {
        if( !POGO_helpers::getPokemonIdFromPokedexId($pokedexId) ) {
            return false;
        }
        return new POGO_pokemon( POGO_helpers::getPokemonIdFromPokedexId($pokedexId), $pokedexId, false );        
    }
    
    
    /**
     * 
     * @return type
     */
    private function _getPokedexId() {
        return get_field('id_pokedex', $this->wpId );
    }
    
    
    /**
     * 
     * @return type
     */
    private function _getNianticId() {
        return get_field('id_niantic', $this->wpId );
    }
    
    public function getNameFr() {
        return get_the_title($this->wpId);
    }
    
    public function _getVariantId() {
        $val = get_field('id_form', $this->wpId);
        if( empty( $val ) ) {
            return false;
        }
        return $val;
    }
    
    
    /**
     * 
     * @return type
     */
    function getBaseStats() {
        return (object) array(
            'attack'    => get_field('base_atk', $this->wpId),
            'defense'    => get_field('base_def', $this->wpId),
            'stamina'    => get_field('base_stam', $this->wpId)
        );
    }
 
    public function getCp( $lvl, $ivAttack, $ivDefense, $ivStamina ) {       
        $cp_multiplier = POGO_helpers::get_cp_scalar($lvl);
        $calc_attack = ($this->getBaseStats()->attack + $ivAttack);
        $calc_defense = ($this->getBaseStats()->defense + $ivDefense);
        $calc_stamina = ($this->getBaseStats()->stamina + $ivStamina);
        $cp = (int)($calc_attack * pow($calc_defense, 0.5) * pow($calc_stamina, 0.5) * pow($cp_multiplier, 2) / 10);
        return $cp;
    }  
    
    public function isShiny() {
        $value = get_field('shiny', $this->wpId);
        if( empty($value) ) {
            return false;
        }
        return true; 
    }
    
    public function isRaidBoss() {
        $value = get_field('raidboss', $this->wpId);
        if( empty($value) ) {
            return false;
        }
        return true;        
    }
    
    public function getRaidLevel() {
        if( !$this->isRaidBoss() ) {
            return false;
        }
        $value = get_field('raidboss_egg', $this->wpId);
        if( empty( $value ) ) {
            return false;
        }
        return $value;         
    }
    
    public function getSearhPatterns() {
        $return = array( strtolower( POGO_helpers::deleteAccents( $this->getNameFr() ) ) );
        $patterns = get_field('search_patterns', $this->wpId);
        if( empty( $patterns ) ) {
            return $return;
        }
        foreach( $patterns as $pattern ) {
            $return[] = strtolower( POGO_helpers::deleteAccents( $pattern['pattern'] ) );
        }
        return $return;
    }
    
    public function getThumbnailUrl() {
        if( $this->variantId ) {
            return 'https://assets.profchen.fr/img/pokemon/pokemon_icon_'.$this->pokedexId.'_'.$this->variantId.'.png';
        }
        return 'https://assets.profchen.fr/img/pokemon/pokemon_icon_'.$this->pokedexId.'_00.png';
    }
    
    public function getParent() {
        if( wp_get_post_parent_id( $this->wpId ) ) {
            return new POGO_pokemon( wp_get_post_parent_id( $this->wpId ) );
        }
        return false;
    }
    
    public function getRaidName() {
        if( $this->getParent() ) {
            return $this->getParent()->getNameFr();
        }
        return $this->getNameFr();
    }
    
}

