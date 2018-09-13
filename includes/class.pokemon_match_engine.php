<?php

class POGO_pokemonMatchEngine {
    
    function __construct( $string, $egg_level = false, $num_test = 1 ) {
        $this->search = $string;
        $this->egg_level = $egg_level;
        $this->compare_to = 'raidbosses';
        $this->num_test = $num_test;
        
        $this->result = (object) array(
            'pokemon' => false,
            'score' => false,
            'bestpattern' => false
        );
        
    }
    
    function compareTo( $compare ) {
        $auth_values = array('raidbosses', 'all');
        if( in_array( $compare, $auth_values ) ) {
            $this->compare_to = $compare;
            return true;
        }
        return false;
    }
    
    function perform() {
        $this->searchLongestPattern();      
    }
    
    function getBestResult( $min_score = null ) {
        error_log('test');
        error_log($this->result->bestpattern);
        return $this->result;
    }
    
    function searchLongestPattern() {     
        
        //Préparation des variables pour le résultat
        $best_proba = false;
        $best_proba_score = 0;
        $submit = POGO_helpers::sanitize($this->search);
        $nb_chars_submit = strlen($this->search);
        
        //Parcours des boos de rais
        $bosses = POGO_helpers::getRaidBosses($this->egg_level);
        error_log('Pahse 1');
        foreach ($bosses as $boss_id) {           

            //Préparation du pokemon à comparer
            $boss = new POGO_pokemon($boss_id);
            $boss_sanitized = POGO_helpers::sanitize($boss->getNameFr());
            
            //On restreint sur les boss de raid de même niveau
            if( !empty($this->egg_level) && $this->egg_level != $boss->getRaidLevel() ) {
                continue;
            }
            
            //S'il n'y en a que un alors on s'embête pas 
            /*if (count($bosses) === 1) {
                $this->result->score = 100;
                $this->result->pokemon = new POGO_pokemon($bosses[0]);
            }*/

            //Parcours
            $debut = 0;
            while( $debut < $nb_chars_submit - 1) {
                
                $fin = $nb_chars_submit - $debut;
                while( $fin > 3 ) {
                    $pattern = mb_strimwidth($submit, $debut, $fin);

                    if( strstr($boss_sanitized, $pattern)) { 
                        $nb_chars_pattern = strlen($pattern);
                        if( $nb_chars_pattern > $this->result->score ) {
                            $this->result->score = $nb_chars_pattern;
                            $this->result->pokemon = $boss;
                        }
                    }                   
                    $fin--;
                }
                
                $debut++;
            }
      
        }
        
        if( $this->result->score == false ) {
            error_log('Pahse 2');
            $bosses_all = POGO_helpers::getRaidBosses();
            foreach ($bosses_all as $boss_all_id) {           

                //Préparation du pokemon à comparer
                $boss_all = new POGO_pokemon($boss_all_id);
                $boss_all_sanitized = POGO_helpers::sanitize($boss_all->getNameFr());

                //Parcours
                $debut = 0;
                while( $debut < $nb_chars_submit - 1) {

                    $fin = $nb_chars_submit - $debut;
                    while( $fin > 3 ) {
                        $pattern = mb_strimwidth($submit, $debut, $fin);

                        if( strstr($boss_all_sanitized, $pattern)) { 
                            $nb_chars_pattern = strlen($pattern);
                            if( $nb_chars_pattern > $this->result->score ) {
                                $this->result->score = $nb_chars_pattern;
                                $this->result->pokemon = $boss_all;
                            }
                        }                   
                        $fin--;
                    }

                    $debut++;
                }

            }            
        }

        //S'il n'y en a que un alors on s'embête pas 
        if( count($bosses) === 1 && $this->result->score == false && $this->num_test === 2 ) {
            error_log('Pahse 3');
            $this->result->score = 100;
            $this->result->pokemon = new POGO_pokemon($bosses[0]);
        }
    }
    
}

