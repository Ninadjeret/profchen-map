<?php

class POGO_messageDecoder {

    const PREFIX = '+';
    const QUEST_COMMAND = 'quete';
    
    function __construct( $message ) {
        $this->message = $message;
        $this->quest = (object) array(
            'reward' => (object) array(
                'type'  => null,
            ), 
        );
    }
    
    function contains( $element ) {
        if( strstr( strtolower(POGO_helpers::deleteAccents($this->message)), strtolower(POGO_helpers::deleteAccents($element)) )) { 
            return true; 
        }         
        return false;
    }
    
    function isAboutQuest() {
        if( $this->contains( POGO_messageDecoder::PREFIX.POGO_messageDecoder::QUEST_COMMAND ) ) {
            return true;
        }
        return false;
    }
    
    function getQuestRewardPokemon() {
        $pokemons = POGO_helpers::getPokemon();
        foreach( $pokemons as $pokemon_id ) {
            $pokemon = new POGO_pokemon($pokemon_id);
            foreach( $pokemon->getSearhPatterns() as $pattern ) {
                if( $this->contains($pattern) ) {
                    return $pokemon;
                }
            } 
        }
        return false;
        
    }
    
    function getQuestReward() {
        $patterns = array('rappel', 'baie', 'bonbon', 'ball');
        foreach( $patterns as $pattern ) {
            if( $this->contains( $pattern ) ) {
                $this->quest->reward->type = $pattern;
            }
        }
    }
    
}

