<?php

class POGO_api {
    
    function __construct() {
        $this->version = 1;
        add_action( 'rest_api_init', array($this, 'registerRoutes') );
        add_filter( 'rest_url_prefix', array($this,'changeRestPrefix') );
    } 
    
    function changeRestPrefix() {
        return 'api';
    } 
    
    public function registerRoutes() {
        $basename = '/v'.$this->version.'/';//(?P<parent>\d+)
        
        //Raid
        register_rest_route( $basename, '/raidbosses/', array(
            'methods' => 'GET',
            'callback' => array( $this, 'getRaidBosses' ),
        ) );
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
        register_rest_route( $basename, '/raids/add/', array(
            'methods' => 'GET',
            'callback' => array( $this, 'addRaid' ),
            'args' => array(
                'token' => array(
                    'required' => true,
                    'validate_callback' => function($param, $request, $key) {
                        return $this->isRightToken($param);
                    }
                ),
                'url' => array(),
                'gymId' => array(
                    'validate_callback' => function($param, $request, $key) {
                        return $this->isValidGym($param);
                    }                    
                ),
                'pokemonId' => array(
                    'validate_callback' => function($param, $request, $key) {
                        return $this->isValidPokemon($param);
                    }                     
                ),
                'eggLevel' => array(
                    'validate_callback' => function($param, $request, $key) {
                        return $this->isValidEggLevel($param);
                    }  
                ),
                'date' => array(
                    'validate_callback' => function($param, $request, $key) {
                        return $this->isValidDate($param);
                    }   
                ),
                'sourceType' => array(
                    'validate_callback' => function($param, $request, $key) {
                        return $this->isValidSourceType($param);
                    }   
                ),
                'sourceCommunity' => array(
                    'validate_callback' => function($param, $request, $key) {
                        return $this->isValidSourceCommunity($param);
                    }   
                ),
                'sourceChannel' => array(
                    'validate_callback' => function($param, $request, $key) {
                        return $this->isValidSourceChannel($param);
                    }   
                ),
                'sourceUser' => array(),
            ),            
        ) ); 
        register_rest_route( $basename, '/raid/(?P<raidId>[0-9-]+)/update', array(
            'methods' => 'POST',
            'callback' => array( $this, 'updateRaid' ),
            'args' => array(
                'token' => array(
                    'required' => true,
                    'validate_callback' => function($param, $request, $key) {
                        return $this->isRightToken($param);
                    }
                ),
                'pokemonId' => array(
                    'required' => true,
                    'validate_callback' => function($param, $request, $key) {
                        return $this->isValidPokemon($param);
                    }                     
                ),
            ),            
        ) ); 
        register_rest_route( $basename, '/raid/(?P<raidId>[0-9-]+)/delete', array(
            'methods' => 'POST',
            'callback' => array( $this, 'deleteRaid' ),
            'args' => array(
                'token' => array(
                    'required' => true,
                    'validate_callback' => function($param, $request, $key) {
                        return $this->isRightToken($param);
                    }
                ),
            ),            
        ) );
        
        //Gyms
        register_rest_route( $basename, '/gyms/', array(
            'methods' => 'GET',
            'callback' => array( $this, 'getGyms' ),
            'args' => array(
                'token' => array(
                    'required' => true,
                    'validate_callback' => function($param, $request, $key) {
                        return $this->isRightToken($param);
                    }
                ),
            ), 
        ) );
        register_rest_route( $basename, '/gym/(?P<gym_id>[0-9-]+)/', array(
            'methods' => 'GET',
            'callback' => array( $this, 'getGym' ),
            'args' => array(
                'token' => array(
                    'required' => true,
                    'validate_callback' => function($param, $request, $key) {
                        return $this->isRightToken($param);
                    }
                ),
            ),            
        ) );
        
        //Quests
        register_rest_route( $basename, '/quests/', array(
            'methods' => 'GET',
            'callback' => array( $this, 'getQuests' ),
        ) );
        register_rest_route( $basename, '/quests/instances/', array(
            'methods' => 'GET',
            'callback' => array( $this, 'getQuestsInstances' ),
        ) );
        register_rest_route( $basename, '/quests/instances/add/', array(
            'methods' => 'POST',
            'callback' => array( $this, 'addQuest' ),
        ) );
        
        //Communities
        register_rest_route( $basename, '/communities/', array(
            'methods' => 'GET',
            'callback' => array( $this, 'getCommunities' ),
            'args' => array(
                'token' => array(
                    'required' => true,
                    'validate_callback' => function($param, $request, $key) {
                        return $this->isRightToken($param);
                    }
                ),
            ), 
        ) );
            
        //NEWS
        register_rest_route( $basename, '/news/', array(
            'methods' => 'GET',
            'callback' => array( $this, 'getNews' ),
            'args' => array(
                'token' => array(
                    'required' => true,
                    'validate_callback' => function($param, $request, $key) {
                        return $this->isRightToken($param);
                    }
                ),
            ),            
        ) );
        
        //Bot config
        register_rest_route( $basename, '/bot/communities/', array(
            'methods' => 'GET',
            'callback' => array( $this, 'getBotCommunities' ),
            'args' => array(
                'token' => array(
                    'required' => true,
                    'validate_callback' => function($param, $request, $key) {
                        return $this->isRightToken($param);
                    }
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
        if( $token == POGO_config::APP_API_PRIVATE_KEY ) {
            return true;
        }
        if( POGO_query::getUserFromSecretKey($token) ) {
            return true;
        }
        return false;
    }
    
    function isValidGym( $gymId ) {
        return ( get_post_type($gymId) == 'gym' ) ? true : false ;
    }
    
    function isValidPokemon( $pokemonId ) {
        return ( get_post_type($pokemonId) == 'pokemon' ) ? true : false ;
    }
    
    function isValidEggLevel( $egglevel ) {
        return ( $egglevel >= 1 && $egglevel <= 5 ) ? true : false ;
    }
    
    function isValidDate( $date_string ) {
        $format = 'Y-m-d H:i:s';
        $d = DateTime::createFromFormat($format, $date_string);
        return $d && $d->format($format) === $date_string;
    }
    
    function isValidSourceType( $type ) {
        $types = array('text', 'img', 'map');
        return ( in_array( $type, $types ) ) ? true : false ;
    }
    
    function isValidSourceCommunity( $communityId ) {
        $communities = POGO_helpers::getCommunities();
        foreach( $communities as $community ) {
            if( $community->externalId == $communityId ) {
                return true;
            }
        }
        return false;
    }
    
    function isValidSourceChannel( $channelId ) {
        if( is_numeric( $channelId ) ) {
            return true;
        }
        return false;
    }
    
    /**
     * =========================================================================
     * RAID METHODS
     * =========================================================================
     */
    
    /**
     * 
     * @return type
     */
    function getRaidBosses() {
        $result = array();
        foreach( POGO_helpers::getRaidBosses() as $pokemon_id ) {
            $pokemon = new POGO_pokemon($pokemon_id);
            $result[] = $this->preparePokemonForExport($pokemon);
        }
        return $result;
    }
    
    /**
     * 
     * @param type $request
     * @return type
     */
    function getRaidDataFromImg( $request ) {
        $url = urldecode( $request->get_param('url') );
        $imageAnalyzer = new POGO_IA_engine($url);
        $result = $imageAnalyzer->result;
        
        $gymName = ( $result->gym ) ? $result->gym->nianticId : false ;
        $bossName = false ;
        $eggLevel = $result->eggLevel;
        
        if( $result->pokemon ) {
            $bossName = $result->pokemon->getNameFr();
        }
        
        $return = (object) array(
            'gym' => $gymName,
            'eggLevel' => $eggLevel,
            'pokemon'   => $bossName,
            'date' => $result->date,
            'error' => $result->error,  
            //'logs' => $result->logs,          
        );
        return rest_ensure_response($return);
    }   
    
    function addRaid( $request ) {
        
        $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        error_log($actual_link);
        
        $params = $request->get_params();
  
        /**
         * ---------------------------------------------------------------------
         * RECUPERATION DES INFOS EN FONCTION DE LA SOURCE
         * ---------------------------------------------------------------------
         */
        //Récupération depuis une Image
        if( !empty($params['url']) ) {
            $url = urldecode( $params['url'] );
            $imageAnalyzer = new POGO_IA_engine($url);
            $result = $imageAnalyzer->result;
            
            if( !$result->error && $result->gym && $result->eggLevel && $result->date ) {
                $pokemon_id = ( $result->pokemon ) ? $result->pokemon->wpId : false ;
                $raid_data = array(
                    'gym'       => $result->gym->wpId,
                    'egglevel'  => $result->eggLevel,
                    'pokemon'   => $pokemon_id,
                    'date'      => $result->date,               
                ); 
                $raid_source = array(
                    'community' => $params['sourceCommunity'],
                    'channel'   => $params['sourceChannel'],
                    'type'      => $params['sourceType'],
                    'user'      => $params['sourceUser'],
                    'content'   => $params['url'],
                );
            } else {
                return rest_ensure_response( array(
                    'wpId' => false,
                    'error' => $result->error,
                ) );                 
            }
            
        }
        
        //Récupération depuis le texte
        elseif( !empty($params['gymId']) && !empty($params['eggLevel']) && !empty($params['date']) ) {
            $raid_data = array(
                'gym'       => $params['gymId'],
                'egglevel'  => $params['eggLevel'],
                'pokemon'   => $params['pokemonId'],
                'date'      => $params['date'],                
            ); 
            $raid_source = array(
                'community' => $params['sourceCommunity'],
                'channel'   => $params['sourceChannel'],
                'type'      => $params['sourceType'],
                'user'      => $params['sourceUser'],
                'content'   => $params['sourceContent'],
            );
        }
        
        //Sinon en renvoei
        else {
            return rest_ensure_response( array(
                'wpId' => false,
                'error' => 'Erreur inconnue',
            ) );               
        }
        
        /**
         * ---------------------------------------------------------------------
         * CREATION DU RAID
         * ---------------------------------------------------------------------
         */
        
        //Construction de l'objet ARENE
        $gym = new POGO_gym($raid_data['gym']);
        
        //S'il y a déja un raid
        if( $gym->getCurrentRaid() ) {
            error_log('Un raid est déja en cours à cet endroit ('.$gym->getCurrentRaid()->wpId.')');

            if( !empty($raid_data['pokemon']) ) {
                $raid = $gym->getCurrentRaid();
                $raid->updateRaid($raid_data['pokemon'], $raid_source);
            }

            return rest_ensure_response($gym->getCurrentRaid());
        } 
        
        //On vérifie qu'aucun raid n'est prévu sur cette arène
        if( $gym->getFutureRaid() ) {
            error_log('Un raid est déja en cours de préparation à cet endroit ('.$gym->getCurrentRaid()->wpId.')');
            return rest_ensure_response($gym->getFutureRaid());
        }
        
        //On crée le raid
        $raid = POGO_raid::create($raid_data, $raid_source);
        //$raid = POGO_raid::create($raid_data);
        if( $raid ) {
            return rest_ensure_response( $raid );
        }
        return rest_ensure_response( array(
            'wpId' => false,
            'error' => 'Erreur inconnue',
        ) );         
    }
    
    public function updateRaid( $request ) {
        $raid_id = $request->get_param('raidId');
        $pokemon_id = $request->get_param('pokemonId');
        
        $raid = new POGO_raid($raid_id);
        if( $raid->getPokemon() ) {
            return;
        }
        
        $source_user = '';
        if( $request->get_param('userId') ) {
            $user = new POGO_user($request->get_param('userId'));
            $source_user = $user->getUsername();
        }
        
        $source = array(
            'user'        => $source_user,
            'type'        => 'map',
            'community'   => false,
            'channel'     => false,
            'content'     => ''
        );
        
        $raid->updateRaid($pokemon_id, $source);
        return;
    }
    
    public function deleteRaid( $request ) {
        $raid_id = $request->get_param('raidId');       
        $raid = new POGO_raid($raid_id);
        $user = POGO_query::getUserFromSecretKey($request->get_param('token'));
        if( $user->isAdmin() ) {
            $raid->delete();
            return true;
        }
        return false;
    }
    
    /**
     * =========================================================================
     * GYM METHODS
     * =========================================================================
     */
    
    /**
     * 
     * @return type
     */
    function getGyms( $request ) {
        $result = array();
        foreach (POGO_helpers::getGyms() as $gym) {
            $result[] = $this->prepareGymForExport($gym);
        }
        
        if( $request->get_param('userId') ) {
            update_field( 'last_visit', date('Y-m-d H:i:s'), 'user_'.$request->get_param('userId') );
        }
        
        return $result;
    }
    
    /**
     * 
     * @param type $request
     * @return type
     */
    function getGym( $request ) {
        $gym_id = $request->get_param('gym_id');
        $gym = new POGO_gym($gym_id);
        return $this->prepareGymForExport($gym);
    }
    
    /**
     * =========================================================================
     * COMMUNITIES METHODS
     * =========================================================================
     */
    
    public function getCommunities() {
        $result = array();
        foreach (POGO_helpers::getCommunities() as $community) {
            $result[] = $this->prepareCommunityForExport($community);
        }
        return $result;        
    }
    
    public function getBotCommunities() {
        $result = array();
        foreach (POGO_helpers::getCommunities() as $community) {
            if( $community->getType() != 'discord' ) continue;
            $result[$community->externalId] = $this->prepareDiscordCommunityForExport($community);
        }
        return $result;             
    }

    /**
     * =========================================================================
     * NEWS METHODS
     * =========================================================================
     */  
    public function getNews() {
        $result = array();
        foreach (POGO_helpers::getNews() as $news) {
            $result[] = $this->prepareNewsForExport( $news );        
        }
        return $result;
    }
        
    
    /**
     * =========================================================================
     * PREPARE METHODS
     * =========================================================================
     */
    
    /**
     * 
     * @param type $gym
     * @return type
     */
    public function prepareGymForExport( $gym ) {
        if( $gym->getFutureRaid() ) {
            $raid = $gym->getFutureRaid();
            $raid_array = array(
                'status'        => 'future',
                'id'            => $raid->wpId,
                'eggLevel'      => $raid->getEggLevel(),
                'pokemon'       => $raid->getPokemon(),
                'startTime'     => $raid->getStartTime()->format('Y-m-d H:i:s'),
                'endTime'       => $raid->getEndTime()->format('Y-m-d H:i:s'),
                'source'        => $this->prepareSourceForExport($raid)
            );
        } elseif( $gym->getCurrentRaid() ) {
            $raid = $gym->getCurrentRaid();
            $raid_array = array(
                'status'        => 'active',
                'id'            => $raid->wpId,
                'eggLevel'      => $raid->getEggLevel(),
                'pokemon'       => $this->preparePokemonForExport($raid->getPokemon()),
                'startTime' => $raid->getStartTime()->format('Y-m-d H:i:s'),
                'endTime' => $raid->getEndTime()->format('Y-m-d H:i:s'),
                'source'        => $this->prepareSourceForExport($raid)
            );            
        } else {
            $raid_array = false;
        }
        
        $community_patterns = false;
        if( $gym->getCommunitySearchPatterns() ) {
            $community_patterns = array();
            foreach( $gym->getCommunitySearchPatterns() as $pattern => $communities ) {
                $community_patterns[$pattern] = array();
                foreach( $communities as $community ) {
                    $community_patterns[$pattern][] = $this->prepareCommunityForExport($community);  
                }
            }
        }
        
        return array(
            'id' => $gym->wpId,
            'nianticId' => $gym->nianticId,
            'nameFr' => $gym->getNameFr(),
            'name_alternatives' => $gym->getSearhPatterns(),
            'city' => $gym->getCity(),
            'GPSCoordinates' => $gym->getGPSCoordinates(),
            'GoogleMapsUrl' => $gym->getGoogleMapsUrl(),
            'communitySearchPatterns' => $community_patterns,
            'raid' => $raid_array,
        );        
    }
    
    public function preparePokemonForExport( $pokemon ) {
        
        if (empty($pokemon)) {
            return false;
        }
        
        $parent = false;
        if( $pokemon->getParent() ) {
            $parent = $this->preparePokemonForExport( $pokemon->getParent() );
        }

        return array(
            'id' => $pokemon->wpId,
            'nianticId' => $pokemon->nianticId,
            'pokedexId' => $pokemon->pokedexId,
            'nameFr' => $pokemon->getNameFr(),
            'raidNameFr' => $pokemon->getRaidName(),
            'name_alternatives' => $pokemon->getSearhPatterns(),
            'eggLevel' => $pokemon->getRaidLevel(),
            'shiny' => $pokemon->isShiny(),
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
    
    public function prepareCommunityForExport( $community ) {
        
        if (empty($community)) {
            return false;
        }

        return array(
            'id'            => $community->wpId,
            'externalId'    => $community->externalId,
            'type'          => $community->getType(),
            'nameFr'        => $community->getNameFr(),
            'url'           => $community->getUrl()
        );
    }
    
    public function prepareNewsForExport( $news_obj ) {
        return array(
            'wpId'              => $news_obj->wpId,
            'nameFr'            => $news_obj->getNameFr(),
            'publishDate'       => $news_obj->getPublishDate()->format('Y-m-d H:i:s'), 
            'contentCommunity'  => $news_obj->getContentForCommunity(),
            'contentMap'        => $news_obj->getContentForMap(),
            'isImportant'       => $news_obj->isImportant(),
        );
    }
    
    public function prepareSourceForExport($raid) {
        if( $raid->getFirstAnnounce() && $raid->getFirstAnnounce()->getType() == 'auto' ) {
            return POGO_helpers::getBotSource();
            
        } elseif( $raid->getFirstAnnounce() && $raid->getFirstAnnounce()->getType() == 'map' ) {
            return POGO_helpers::getMapSource();
            
        }elseif( $raid->getFirstAnnounce() && $raid->getFirstAnnounce()->getCommunity() ) {
            return array(
                'user'        => $raid->getLastAnnounce()->getAuthor(),
                'type'        => $raid->getFirstAnnounce()->getType(),
                'community'   => $this->prepareCommunityForExport( $raid->getFirstAnnounce()->getCommunity() ),
                'channel'     => $raid->getFirstAnnounce()->getChannelId(),  
                'content'     => '',
                'url'         => $raid->getFirstAnnounce()->getsourceUrl(),  
            );
        } 
        
        return false;
    }
    
    public function prepareDiscordCommunityForExport( $community ) {
        
        if (empty($community)) {
            return false;
        }
        
        $params = array(
            'news' => array(
                'niantic'   => array(
                    'active'    => $community->displayNianticNews(),
                    'channel'   => $community->getNianticNewsChannel()
                ),
                'silphroad'   => array(
                    'active'    => $community->displaySilphroadNews(),
                    'channel'   => $community->getSilphoradNewsChannel()
                ),
                'gohub'   => array(
                    'active'    => $community->displayGohubNews(),
                    'channel'   => $community->getGohubNewsChannel()
                ),
            ),
            'memberAdd' => array(
                'active'    => $community->welcomeNewMembers(),
                'channel'   => $community->getNewMemberChannel(),
                'message'   => $community->getNewMemberMessage()
            ),
            'raidDetection' => array(
                'active'        => $community->detectRaids(),
                'deleteMessage' => $community->deleteMessageAfterRaidDetection()
            ),            
        );
        
        $commands = array(
            'prefix' => '+',
            'activate' => 'activation',
            'deactivate' => 'desactivation',
            'raid' => 'raid'
        );
        
        return array(
            'id'            => $community->wpId,
            'externalId'    => $community->externalId,
            'type'          => $community->getType(),
            'discordName'   => $community->getNameFr(),
            'active'        => true,
            'url'           => $community->getUrl(),
            'params'        => $params,
            'commands'      => $commands,
        );
    }

   

}

new POGO_api();
