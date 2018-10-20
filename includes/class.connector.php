    <?php

class POGO_connector {
    
    function __construct( $id ) {
        $this->wpId = $id;
        $this->url = get_field('url', $this->wpId);
    }
    
    public function getNameFr() {
        return get_field('name_fr', $this->wpId);
        //return html_entity_decode( get_the_title($this->wpId) );
    }
    
    /**
     * Format Data to prepare sending to Discord (embed message format)
     * 
     * @author Floflo
     * @since 2018-06-10
     * 
     * @param \POGO_raid $raid
     * @return array
     */
    public function formatData( $raid ) {
        
        $description = '';
        $title = 'Raid '.$raid->getEggLevel().' têtes';
        $img_url = "https://assets.profchen.fr/img/eggs/egg_".$raid->getEggLevel().".png";
        if( $raid->getStartTime() ) {
            $title .= ' à '.$raid->getStartTime()->format('H\hi');
            $description = "Pop : de ".$raid->getStartTime()->format('H\hi')." à ".$raid->getEndTime()->format('H\hi');
        }
        
        if( $raid->getPokemon() ) {           
            $title = html_entity_decode('Raid '.$raid->getPokemon()->getNameFr().' jusqu\'à '.$raid->getEndTime()->format('H\hi'));
            $img_url = $raid->getPokemon()->getThumbnailUrl();
        }

        $data = array(
            "content" => "",
            "username" => "Assistant du Prof Chen",
            "avatar_url" => "https://cdn.discordapp.com/app-icons/426100368628514836/eb1de647b7d6c9612f2f457defa1242a.png",
            "embeds" => array(array(
                    "author" => array(
                        "name" => $raid->getGym()->getCity().' - '.$raid->getGym()->getNameFr(),
                        "icon_url" => "https://d30y9cdsu7xlg0.cloudfront.net/png/4096-200.png",
                        "url" => $raid->getGym()->getGoogleMapsUrl(),
                    ),
                    "title" => $title,
                    "description" => $description,
                    "thumbnail" => array( "url" => $img_url ),
                    "color" => $this->getEggColor( $raid->getEggLevel() ),
                ))
        );
        
        if( $this->displaySource() ) {
            if( $raid->getLastAnnounce() && $raid->getLastAnnounce()->getType() == 'auto' ) {
                $data['embeds'][0]['footer']['text'] = 'Annonce automatique du Prof Chen';
            } elseif( $raid->getLastAnnounce() && $raid->getLastAnnounce()->getType() == 'map' ) {
                $data['embeds'][0]['footer']['text'] = 'Mis à jour depuis la map';
            } elseif( $raid->getLastAnnounce() && $raid->getLastAnnounce()->getCommunity() ) {
                $data['embeds'][0]['footer']['text'] = 'Annoncé depuis le '.$raid->getLastAnnounce()->getCommunity()->getNameFr();
            } else {
                //$embed['embeds'][0]['footer']['text'] = 'Sans info';
            }           
        }
        
        //return $data;
        return apply_filters('pogo/raid/annonce/embedData', $data, $raid, $this->wpId);
    }
    
    public function formatData2( $raid ) {
        
        //Check
        if( !$this->getCommunity() ) return false;
        
        $embed = $this->formatData($raid);
        return apply_filters('pogo/raid/annonce/botData', array(
            'serverId' => $this->getCommunity()->externalId,
            'channelName' => '',
            'embed' => $embed,
        ));
    }
    
    /**
     * Send formatted emebed message to Discord webhook
     * 
     * @author Floflo
     * @since 2018-06-10
     * 
     * @param array $data
     */
    public function sendToWebhook( $data, $url = null ) {
        $url = ( empty( $url ) ) ? $this->url : $url ;
        $data_string = json_encode($data);
        $ch = curl_init($url);
        //$ch = curl_init('https://discordapp.com/api/webhooks/454871314247712769/cml6WTXejSEm3zJrczkcbNc8IBUBEaf9gvw-rbqoig831uUrT03XJ27o2MqBAsLWIvJY');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_string))
        );
        $result = curl_exec($ch); 
    }  

    
    /**
     * Get egg color for emebed message left border
     * 
     * @author Floflo
     * @since 2018-06-10
     * 
     * @param type $eggLevel
     * @return boolean
     */
    public function getEggColor( $eggLevel ) {
        $colors = array(
            1 => 'de6591',
            2 => 'de6591',
            3 => 'efad02',
            4 => 'efad02',
            5 => '222',
        );
        
        if(array_key_exists($eggLevel, $colors) ) {
            return hexdec( $colors[$eggLevel] );
        }
        return false;
    }
    
    function displaySource() {
        $val = get_field('connector_display_source', $this->wpId);
        if( empty($val) ) {
            return false;
        }
        return true;
    }
    
    function getCommunity() {
        $val = get_field('connector_community', $this->wpId);
        if( empty($val) ) {
            return false;
        }
        return new POGO_community($val);
    }
    
    function getDeleteLink() {
        return home_url().'?profWillDo=delete&pid='.$this->wpId;
    }
    
    public function delete() {
        wp_trash_post( $this->wpId );
    }
    
    public function filterGyms() {
        $val = get_field('connector_filter_gyms', $this->wpId);
        if( empty($val) || $val == 'none' ) {
            return false;
        }
        return $val;        
    }
    
    public function getFilteredCities() {
        $val = get_field('connector_cities', $this->wpId);
        if( empty($val) ) {
            return false;
        }
        return $val;
    }
    
    public function getFilteredGyms() {
        $val = get_field('connector_gyms', $this->wpId);
        if( empty($val) ) {
            return false;
        }
        return $val;        
    }
    
    public function filterPokemon() {
        $val = get_field('connector_filter_pokemon', $this->wpId);
        if( empty($val) || $val == 'none' ) {
            return false;
        }
        return $val;        
    }
    
    public function getFilteredLevels() {
        $val = get_field('connector_egglevels', $this->wpId);
        if( empty($val) ) {
            return false;
        }
        return $val;
    }
    
    public function getFilteredPokemon() {
        $val = get_field('connector_pokemon', $this->wpId);
        if( empty($val) ) {
            return false;
        }
        return $val;        
    }
}

