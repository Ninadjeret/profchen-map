<?php

class POGO_helpers {
   
    /**
     * 
     * @param type $lvl
     * @param type $attack
     * @param type $defense
     * @param type $stamina
     * @return type
     */
    public static function get_max_cp_for_lvl( $lvl, $attack, $defense, $stamina ) {       
        $cp_multiplier =  POGO_helpers::get_cp_scalar($lvl);
        $calc_attack = ($attack + 10);
        $calc_defense = ($defense + 10);
        $calc_stamina = ($stamina + 10);
        $cp = (int)($calc_attack * pow($calc_defense, 0.5) * pow($calc_stamina, 0.5) * pow($cp_multiplier, 2) / 10);
        return $cp;
    }
    
    
    /**
     * 
     * @param type $lvl
     * @return type
     */
    public static function get_cp_scalar( $lvl ) {

        $lvls_scalar =array(
            '1'    => 0.094,
            '1.5'  => 0.135137432,
            '2'    => 0.16639787,
            '2.5'  => 0.192650919,
            '3'    => 0.21573247,
            '3.5'  => 0.236572661,
            '4'    => 0.25572005,
            '4.5'  => 0.273530381,
            '5'    => 0.29024988,
            '5.5'  => 0.306057377,
            '6'    => 0.3210876,
            '6.5'  => 0.335445036,
            '7'    => 0.34921268,
            '7.5'  => 0.362457751,
            '8'    => 0.37523559,
            '8.5'  => 0.387592406,
            '9'    => 0.39956728,
            '9.5'  => 0.411193551,
            '10'   => 0.42250001,
            '10.5' => 0.432926419,
            '11'   => 0.44310755,
            '11.5' => 0.453059958,
            '12'   => 0.46279839,
            '12.5' => 0.472336083,
            '13'   => 0.48168495,
            '13.5' => 0.4908558,
            '14'   => 0.49985844,
            '14.5' => 0.508701765,
            '15'   => 0.51739395,
            '15.5' => 0.525942511,
            '16'   => 0.53435433,
            '16.5' => 0.542635767,
            '17'   => 0.55079269,
            '17.5' => 0.558830576,
            '18'   => 0.56675452,
            '18.5' => 0.574569153,
            '19'   => 0.58227891,
            '19.5' => 0.589887917,
            '20'   => 0.59740001,
            '20.5' => 0.604818814,
            '21'   => 0.61215729,
            '21.5' => 0.619399365,
            '22'   => 0.62656713,
            '22.5' => 0.633644533,
            '23'   => 0.64065295,
            '23.5' => 0.647576426,
            '24'   => 0.65443563,
            '24.5' => 0.661214806,
            '25'   => 0.667934,
            '25.5' => 0.674577537,
            '26'   => 0.68116492,
            '26.5' => 0.687680648,
            '27'   => 0.69414365,
            '27.5' => 0.700538673,
            '28'   => 0.70688421,
            '28.5' => 0.713164996,
            '29'   => 0.71939909,
            '29.5' => 0.725571552,
            '30'   => 0.7317,
            '30.5' => 0.734741009,
            '31'   => 0.73776948,
            '31.5' => 0.740785574,
            '32'   => 0.74378943,
            '32.5' => 0.746781211,
            '33'   => 0.74976104,
            '33.5' => 0.752729087,
            '34'   => 0.75568551,
            '34.5' => 0.758630378,
            '35'   => 0.76156384,
            '35.5' => 0.764486065,
            '36'   => 0.76739717,
            '36.5' => 0.770297266,
            '37'   => 0.7731865,
            '37.5' => 0.776064962,
            '38'   => 0.77893275,
            '38.5' => 0.781790055,
            '39'   => 0.78463697,
            '39.5' => 0.787473578,
            '40'   => 0.79030001,
        );
        return $lvls_scalar[$lvl];
    }
    
    /**
     * 
     * @param type $id
     * @return boolean
     */
    public static function getPokemonIdFromPokedexId( $id ) {
        $matching_pokemon = get_posts( array(
            'post_type'         => 'pokemon',
            'posts_per_page'    => 1,
            'fields'            => 'ids',
            'meta_query'        => array(
                array(
                    'key'       => 'id_pokedex',
                    'value'     => $id,
                    'compare'   => '='
                )
            )
        ) );
        
        if( empty( $matching_pokemon ) ) {
            return false;
        }
        
        return $matching_pokemon[0];
    }
    
    /**
     * 
     * @param type $id
     * @return boolean
     */
    public static function getPokemonIdFromNianticId( $id ) {
        $matching_pokemon = get_posts( array(
            'post_type'         => 'pokemon',
            'posts_per_page'    => 1,
            'fields'            => 'ids',
            'meta_query'        => array(
                array(
                    'key'       => 'id_niantic',
                    'value'     => $id
                )
            )
        ) );
        
        if( empty( $matching_pokemon ) ) {
            return false;
        }

        return $matching_pokemon[0];
    }
    
    public static function getCommunityIdFromExternalId( $id ) {
        $matching_community = get_posts( array(
            'post_type'         => 'community',
            'posts_per_page'    => 1,
            'fields'            => 'ids',
            'meta_query'        => array(
                array(
                    'key'       => 'community_discordid',
                    'value'     => $id,
                    'compare'   => '='
                )
            )
        ) );
        
        if( empty( $matching_community ) ) {
            return false;
        }
        
        return $matching_community[0];
    }
  
    /**
     * 
     * @return boolean
     */
    public static function getRaidBosses( $egglevel = false ) {
        
        $args = array(
            'post_type'         => 'pokemon',
            'posts_per_page'    => -1,
            'fields'            => 'ids',
            'orderby'           => 'meta_value_num',
            'meta_key'          => 'id_pokedex',
            'order'             => 'ASC',
            'meta_query'        => array(
                array(
                    'key'       => 'raidboss',
                    'value'     => 1,
                    'compare'   => '='
                )
            )
        );
        
        if( !empty( $egglevel ) ) {
            $args['meta_query'][] = array(
                'key'       => 'raidboss_egg',
                'value'     => $egglevel,
                'compare'   => '=',
            );
        }
        
        $pokemon = get_posts( $args ); 
        
        if( empty( $pokemon ) ) {
            return false;
        }

        return $pokemon;        
        
    }
    
    public static function getPokemon() {
        $pokemon = get_posts( array(
            'post_type'         => 'pokemon',
            'posts_per_page'    => -1,
            'fields'            => 'ids',
            'orderby'           => 'meta_value_num',
            'meta_key'          => 'id_pokedex',
            'order'             => 'ASC',
        ) ); 
        
        if( empty( $pokemon ) ) {
            return false;
        }

        return $pokemon;        
        
    }
    
    /**
     * 
     * @return boolean
     */
    public static function getGyms() {
        $gyms = get_posts( array(
            'post_type'         => 'gym',
            'posts_per_page'    => -1,
            'fields'            => 'ids',
            'orderby'           => 'name',
            'order'             => 'ASC',
        ) ); 
        
        if( empty( $gyms ) ) {
            return false;
        }
        
        $return = array();
        foreach($gyms as $gym_id) {
            $return[] = new POGO_gym($gym_id);
        }

        return $return;        
        
    }
    
    public static function getCommunities() {
        $communities = get_posts( array(
            'post_type'         => 'community',
            'posts_per_page'    => -1,
            'fields'            => 'ids',
            'orderby'           => 'name',
            'order'             => 'ASC',
        ) ); 
        
        if( empty( $communities ) ) {
            return false;
        }
        
        $return = array();
        foreach($communities as $community_id) {
            $return[] = new POGO_community($community_id);
        }

        return $return;        
        
    }
    
    public static function getNews() {
        $news = get_posts( array(
            'post_type'         => 'bot_update',
            'posts_per_page'    => 10,
            'fields'            => 'ids',
        ) ); 
        
        if( empty( $news ) ) {
            return false;
        }
        
        $return = array();
        foreach($news as $news_id) {
            $return[] = new POGO_news($news_id);
        }

        return $return;        
        
    }
    
    public static function hasAccents($string) {
        $pattern = "#^[a-z0-9]+$#i";
        if (preg_match($pattern, $string)) {
            return false;
        }
        return true;
    }

    /*public static function deleteAccents($str) {
        $ch = strtr($str, 'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ', 'aaaaaceeeeiiiiooooouuuuyaaaaaaceeeeiiiioooooouuuuyy');
        return $ch;
    }*/

public static function deleteAccents($string) {
    if ( !preg_match('/[\x80-\xff]/', $string) )
        return $string;

    $chars = array(
    // Decompositions for Latin-1 Supplement
    chr(195).chr(128) => 'A', chr(195).chr(129) => 'A',
    chr(195).chr(130) => 'A', chr(195).chr(131) => 'A',
    chr(195).chr(132) => 'A', chr(195).chr(133) => 'A',
    chr(195).chr(135) => 'C', chr(195).chr(136) => 'E',
    chr(195).chr(137) => 'E', chr(195).chr(138) => 'E',
    chr(195).chr(139) => 'E', chr(195).chr(140) => 'I',
    chr(195).chr(141) => 'I', chr(195).chr(142) => 'I',
    chr(195).chr(143) => 'I', chr(195).chr(145) => 'N',
    chr(195).chr(146) => 'O', chr(195).chr(147) => 'O',
    chr(195).chr(148) => 'O', chr(195).chr(149) => 'O',
    chr(195).chr(150) => 'O', chr(195).chr(153) => 'U',
    chr(195).chr(154) => 'U', chr(195).chr(155) => 'U',
    chr(195).chr(156) => 'U', chr(195).chr(157) => 'Y',
    chr(195).chr(159) => 's', chr(195).chr(160) => 'a',
    chr(195).chr(161) => 'a', chr(195).chr(162) => 'a',
    chr(195).chr(163) => 'a', chr(195).chr(164) => 'a',
    chr(195).chr(165) => 'a', chr(195).chr(167) => 'c',
    chr(195).chr(168) => 'e', chr(195).chr(169) => 'e',
    chr(195).chr(170) => 'e', chr(195).chr(171) => 'e',
    chr(195).chr(172) => 'i', chr(195).chr(173) => 'i',
    chr(195).chr(174) => 'i', chr(195).chr(175) => 'i',
    chr(195).chr(177) => 'n', chr(195).chr(178) => 'o',
    chr(195).chr(179) => 'o', chr(195).chr(180) => 'o',
    chr(195).chr(181) => 'o', chr(195).chr(182) => 'o',
    chr(195).chr(182) => 'o', chr(195).chr(185) => 'u',
    chr(195).chr(186) => 'u', chr(195).chr(187) => 'u',
    chr(195).chr(188) => 'u', chr(195).chr(189) => 'y',
    chr(195).chr(191) => 'y',
    // Decompositions for Latin Extended-A
    chr(196).chr(128) => 'A', chr(196).chr(129) => 'a',
    chr(196).chr(130) => 'A', chr(196).chr(131) => 'a',
    chr(196).chr(132) => 'A', chr(196).chr(133) => 'a',
    chr(196).chr(134) => 'C', chr(196).chr(135) => 'c',
    chr(196).chr(136) => 'C', chr(196).chr(137) => 'c',
    chr(196).chr(138) => 'C', chr(196).chr(139) => 'c',
    chr(196).chr(140) => 'C', chr(196).chr(141) => 'c',
    chr(196).chr(142) => 'D', chr(196).chr(143) => 'd',
    chr(196).chr(144) => 'D', chr(196).chr(145) => 'd',
    chr(196).chr(146) => 'E', chr(196).chr(147) => 'e',
    chr(196).chr(148) => 'E', chr(196).chr(149) => 'e',
    chr(196).chr(150) => 'E', chr(196).chr(151) => 'e',
    chr(196).chr(152) => 'E', chr(196).chr(153) => 'e',
    chr(196).chr(154) => 'E', chr(196).chr(155) => 'e',
    chr(196).chr(156) => 'G', chr(196).chr(157) => 'g',
    chr(196).chr(158) => 'G', chr(196).chr(159) => 'g',
    chr(196).chr(160) => 'G', chr(196).chr(161) => 'g',
    chr(196).chr(162) => 'G', chr(196).chr(163) => 'g',
    chr(196).chr(164) => 'H', chr(196).chr(165) => 'h',
    chr(196).chr(166) => 'H', chr(196).chr(167) => 'h',
    chr(196).chr(168) => 'I', chr(196).chr(169) => 'i',
    chr(196).chr(170) => 'I', chr(196).chr(171) => 'i',
    chr(196).chr(172) => 'I', chr(196).chr(173) => 'i',
    chr(196).chr(174) => 'I', chr(196).chr(175) => 'i',
    chr(196).chr(176) => 'I', chr(196).chr(177) => 'i',
    chr(196).chr(178) => 'IJ',chr(196).chr(179) => 'ij',
    chr(196).chr(180) => 'J', chr(196).chr(181) => 'j',
    chr(196).chr(182) => 'K', chr(196).chr(183) => 'k',
    chr(196).chr(184) => 'k', chr(196).chr(185) => 'L',
    chr(196).chr(186) => 'l', chr(196).chr(187) => 'L',
    chr(196).chr(188) => 'l', chr(196).chr(189) => 'L',
    chr(196).chr(190) => 'l', chr(196).chr(191) => 'L',
    chr(197).chr(128) => 'l', chr(197).chr(129) => 'L',
    chr(197).chr(130) => 'l', chr(197).chr(131) => 'N',
    chr(197).chr(132) => 'n', chr(197).chr(133) => 'N',
    chr(197).chr(134) => 'n', chr(197).chr(135) => 'N',
    chr(197).chr(136) => 'n', chr(197).chr(137) => 'N',
    chr(197).chr(138) => 'n', chr(197).chr(139) => 'N',
    chr(197).chr(140) => 'O', chr(197).chr(141) => 'o',
    chr(197).chr(142) => 'O', chr(197).chr(143) => 'o',
    chr(197).chr(144) => 'O', chr(197).chr(145) => 'o',
    chr(197).chr(146) => 'OE',chr(197).chr(147) => 'oe',
    chr(197).chr(148) => 'R',chr(197).chr(149) => 'r',
    chr(197).chr(150) => 'R',chr(197).chr(151) => 'r',
    chr(197).chr(152) => 'R',chr(197).chr(153) => 'r',
    chr(197).chr(154) => 'S',chr(197).chr(155) => 's',
    chr(197).chr(156) => 'S',chr(197).chr(157) => 's',
    chr(197).chr(158) => 'S',chr(197).chr(159) => 's',
    chr(197).chr(160) => 'S', chr(197).chr(161) => 's',
    chr(197).chr(162) => 'T', chr(197).chr(163) => 't',
    chr(197).chr(164) => 'T', chr(197).chr(165) => 't',
    chr(197).chr(166) => 'T', chr(197).chr(167) => 't',
    chr(197).chr(168) => 'U', chr(197).chr(169) => 'u',
    chr(197).chr(170) => 'U', chr(197).chr(171) => 'u',
    chr(197).chr(172) => 'U', chr(197).chr(173) => 'u',
    chr(197).chr(174) => 'U', chr(197).chr(175) => 'u',
    chr(197).chr(176) => 'U', chr(197).chr(177) => 'u',
    chr(197).chr(178) => 'U', chr(197).chr(179) => 'u',
    chr(197).chr(180) => 'W', chr(197).chr(181) => 'w',
    chr(197).chr(182) => 'Y', chr(197).chr(183) => 'y',
    chr(197).chr(184) => 'Y', chr(197).chr(185) => 'Z',
    chr(197).chr(186) => 'z', chr(197).chr(187) => 'Z',
    chr(197).chr(188) => 'z', chr(197).chr(189) => 'Z',
    chr(197).chr(190) => 'z', chr(197).chr(191) => 's'
    );

    $string = strtr($string, $chars);

    return $string;
}
    
    public static function findExstingGym( $text ) {
        foreach (POGO_query::getGyms() as $gym) {
            foreach( $gym->getSearhPatterns() as $pattern ) {
                if( strstr($text, $pattern) ) {
                    return $gym; 
                }
            }
        }
        return false;
    }
    
    public static function sendToWebhook( $webhook_url, $data ) {
        $data_string = json_encode($data);
        $ch = curl_init($webhook_url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_string))
        );

        $result = curl_exec($ch);        
    }
    
    public static function sanitize( $string ) {
        $return = POGO_helpers::deleteAccents($string);
        return strtolower($return);
    }
    
    public static function getMatchingConnectors( $raid ) { 
        
        $source_type = '';
        if( $raid->getLastAnnounce() && $raid->getLastAnnounce()->getType() ) {
            $source_type = $raid->getLastAnnounce()->getType();
        }
        
        $args = array(
            'post_type' => 'connector',
            'posts_per_page' => -1,
            'fields' => 'ids',
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'relation' => 'OR',
                    array(
                        'key' => 'connector_gyms',
                        'compare' => 'NOT EXISTS'
                    ),
                    array(
                        'key' => 'connector_gyms',
                        'value' => '',
                        'compare' => '='
                    ),
                    array(
                        'key' => 'connector_gyms',
                        'value' => '"' . $raid->getGym()->wpId . '"',
                        'compare' => 'LIKE'
                    ),
                ),
                array(
                    'relation' => 'OR',
                    array(
                        'key' => 'connector_egglevels',
                        'compare' => 'NOT EXISTS'
                    ),
                    array(
                        'key' => 'connector_egglevels',
                        'value' => '',
                        'compare' => '='
                    ),
                    array(
                        'key' => 'connector_egglevels',
                        'value' => '"' . $raid->getEggLevel() . '"',
                        'compare' => 'LIKE'
                    ),
                ),
                array(
                    'relation' => 'OR',
                    array(
                        'key' => 'connector_raidstep',
                        'compare' => 'NOT EXISTS'
                    ),
                    array(
                        'key' => 'connector_raidstep',
                        'value' => '',
                        'compare' => '='
                    ),
                    array(
                        'key' => 'connector_raidstep',
                        'value' => '"' . $raid->getStatus() . '"',
                        'compare' => 'LIKE'
                    ),
                ),
                 array(
                    'relation' => 'OR',
                    array(
                        'key' => 'connector_source_type',
                        'compare' => 'NOT EXISTS'
                    ),
                    array(
                        'key' => 'connector_source_type',
                        'value' => '',
                        'compare' => '='
                    ),
                    array(
                        'key' => 'connector_source_type',
                        'value' => '"' . $source_type . '"',
                        'compare' => 'LIKE'
                    ),
                ),
            ),
        );

        $results = get_posts( $args );
        
        if( empty( $results ) ) {
            return false;
        }
        
        $connectors = array();
        foreach( $results as $connector_id ) {
            $connectors[] = new POGO_connector($connector_id);
        }
        
        return $connectors;
    }
    
    public static function getPostContent( $post_id ) {
        $content_post = get_post($post_id);
        $content = $content_post->post_content;
        $content = apply_filters('the_content', $content);
        $content = str_replace(']]>', ']]&gt;', $content);
        return $content;        
    }
    
    public static function getRaids( $status = array('active', 'future') ) {
        if(is_string($status) ) $status = array($status);
        $gyms = POGO_query::getGyms();
        $raids = array();
        foreach( $gyms as $gym ) {
            if( in_array('active', $status) && $gym->getCurrentRaid() ) {
                $raids[] = $gym->getCurrentRaid();
            }
            if( in_array('future', $status) && $gym->getFutureRaid() ) {
                $raids[] = $gym->getFutureRaid();
            }
        }
        return $raids;
    }
    
    public static function isPrivate( $page_id ) {
        if( get_post_status( $page_id ) == 'private' ) {
            return true;
        }
        return false;
    }
    
    public static function getBotSource() {
        return array(
            'user'        => 'Prof Chen',
            'type'        => 'auto',
            'community'   => false,
            'channel'     => false,
            'content'     => 'Mise à jour automatique',
            'url'         => false
        );
    }
    
    public static function getMapSource() {
        return array(
            'user'        => false,
            'type'        => 'map',
            'community'   => false,
            'channel'     => false,
            'content'     => 'Annoncé depuis la map',
            'url'         => false
        );
    }
    
    public static function getCities() {
        return apply_filters('pogo/cities', array('Beaulieu', 'Bourgbarré', 'Bruz', 'Cesson', 'Chartres', 'Nouvoitou', 'Noyal', 'Orgères', 'Pont-Péan', 'Rennes Sud', 'Saint-Erblon', 'Saint-Armel', 'Vern'));
    }
}
