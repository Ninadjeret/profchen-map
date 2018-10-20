<?php

class POGO_imageAnalyzer {

    var $debug = true;
    var $image = false;
    var $image_path = false;
    var $image_type = false;
    
    /**
     * 
     * @param type $url
     */
    function __construct( $url ) {
        
        //Tools
        require_once( get_stylesheet_directory() . '/vendor/autoload.php');
        $this->tesseract = new Ddeboer\Tesseract\Tesseract();
        $this->gymSearch = new POGO_IA_gymSearch();
        
        //Result
        $this->result = (object) array(
            'gym' => false,
            'eggLevel' => false,
            'pokemon'   => false,
            'date' => false,
            'error' => false,  
            'logs' => '',         
        );
        
        $this->start = microtime(true);
        if( $this->debug ) $this->_log('========== Début du traitement '.$url.' ==========');
 
        $this->image_url = $url;
        $this->_saveImage();
        
        $this->coordinates = new POGO_IA_coordinates($this->image_width, $this->image_height);
        
        $this->_createImageForBasicIdentification();
        $this->image_type = $this->_getImgType();
        //$this->image_type = 'egg';
        
        //For Pokemon IMG
        if( $this->image_type == 'pokemon' ) {
            $this->_createImageForGymSearch();
            $this->_createImageForPokemonsearch();
            $this->_createImageForPokemonTimeSearch(); 
            
            $this->result->gym = $this->_getGym();    
            $this->result->eggLevel = $this->_getEggLevelForActiveRaid();
            $this->result->pokemon = $this->_getPokemon( $this->result->eggLevel );
            $this->result->date = $this->_getTime();
        } 
        
        //For Egg IMG
        elseif( $this->image_type == 'egg'  ) {
            $this->_createImageForGymSearch();
            $this->_createImageForEggTimeSearch(); 
            
            $this->result->gym = $this->_getGym();
            $this->result->eggLevel = $this->_getEggLevel();
            $this->result->date = $this->_getTime();            
        } 
        
        if( !$this->result->gym && empty( $this->result->error ) ) {
            $this->result->error = 'L\'arène n\'a pas été trouvée.';
        }
        
        $this->_close(); 
        return;
        
        /*if( ( $this->_isRaidAnnounceImg() ) && empty( $this->result->error ) ) {
            $this->_createImageForGymSearch();
            $this->_createImageForTimeSearch(); 
            if( $this->_isPokemonImg()  ) {
                $this->_createImageForPokemonsearch();
            }
            $this->_perform();                   
        } 
        $this->_close();  */          

        
    }
 
    /**
     * =========================================================================
     * GENERATES IMAGES & MAIN METHODS
     * =========================================================================
     */
    
    private function _log( $text ) {
        if( is_array( $text ) ) {
            error_log( print_r($text, true) );
        } else {
            $this->result->logs .= "{$text}\r\n";
            error_log( $text );
        }
    }
    
    /**
     * 
     */
    private function _saveImage() {
        
        $ext = pathinfo($this->image_url, PATHINFO_EXTENSION);
        if( $this->debug ) $this->_log('Img extension : ' . $ext);
        
        $this->filename = 'capture-' . time();
        $this->image_path = get_stylesheet_directory() . '/captures/' . $this->filename . '.jpg';
        
        if( strstr($ext, 'jpg') || strstr($ext, 'jpeg') ) {
            $this->image = imagecreatefromjpeg($this->image_url);
        } elseif( strstr($ext, 'png') ) {
            $this->image = imagecreatefrompng($this->image_url);
        } else {
            $this->result->error = 'File type not allowed';
        }
        
        imagejpeg($this->image, $this->image_path);
        $size = getimagesize($this->image_path);
        $this->image_height = $size[1];
        $this->image_width = $size[0];
        
        //If image do not have good ratio
        if( $this->debug ) $this->_log('Img ratio : ' . $this->_getImageRatio());
        if( $this->_getImageRatio() >= 1 ) {
            $this->result->error = 'Le ratio de l\'image ne semble pas correct';
        }
        
        //If image has android bar
        if( $this->debug ) $this->_log( 'First pixel : ' . $this->getFirstPixel() );
        if( $this->debug ) $this->_log( 'Last pixel : ' . $this->getLastPixel() );
        if( $this->getFirstPixel() > 1 || $this->getLastPixel() < ($this->image_height - 1) ) {
            if( $this->debug ) $this->_log( 'Image has android bar. Crop to get needed size' );
            $this->cropImage();
            $this->image = imagecreatefromjpeg($this->image_path);
            $size = getimagesize($this->image_path);
            $this->image_height = $size[1];
            $this->image_width = $size[0];            
        }
        
    }
    
    /**
     * 
     */
    private function _createImageForBasicIdentification() {
        if( !$this->image_path ) {
            return false;
        }
        $this->image_basic = imagecreatefromjpeg($this->image_path);
        $this->image_basic_path =  get_stylesheet_directory() . '/captures/' . $this->filename . '-basic.jpg'; 
        imagefilter($this->image_basic, IMG_FILTER_PIXELATE, $this->image_width / 2 );
        imagejpeg($this->image_basic, $this->image_basic_path); 
        imagedestroy($this->image_basic);         
    }
    
    /**
     * 
     */
    private function _createImageForGymSearch() {        
        $this->image_gym = imagecrop($this->image, ['x' => $this->coordinates->forImgCropGym()->x, 'y' => $this->coordinates->forImgCropGym()->y, 'width' => $this->coordinates->forImgCropGym()->width, 'height' => $this->coordinates->forImgCropGym()->height]);
        $this->image_gym_path =  get_stylesheet_directory() . '/captures/' . $this->filename . '-gym.jpg';  
        imagejpeg($this->image_gym, $this->image_gym_path); 
        imagedestroy($this->image_gym);         
    }
    
    /**
     * 
     */
    private function _createImageForPokemonsearch() {       
        $this->image_pokemon = imagecrop($this->image, ['x' => 0, 'y' => $this->image_height * 0.2474, 'width' => $this->image_width, 'height' => $this->image_height * 0.072916]);
        $this->image_pokemon_path =  get_stylesheet_directory() . '/captures/' . $this->filename . '-pokemon.jpg';  
        imagejpeg($this->image_pokemon, $this->image_pokemon_path); 
        imagedestroy($this->image_pokemon);         
    }
    
    /**
     * 
     */
    private function _createImageForEggTimeSearch() {
        $this->image_time = imagecrop($this->image, [
            'x' => $this->coordinates->forImgCropTime()->x, 
            'y' => $this->coordinates->forImgCropTime()->y, 
            'width' => $this->coordinates->forImgCropTime()->width, 
            'height' => $this->coordinates->forImgCropTime()->height
                ]);
        $this->image_time_path =  get_stylesheet_directory() . '/captures/' . $this->filename . '-time.jpg';  
        imagefilter($this->image_time, IMG_FILTER_BRIGHTNESS, -130 );
        imagefilter($this->image_time, IMG_FILTER_GRAYSCALE );
        imagefilter($this->image_time, IMG_FILTER_CONTRAST, -70 );        
        imagejpeg($this->image_time, $this->image_time_path); 
        imagedestroy($this->image_time);        
    }
    
    /**
     * 
     */
    private function _createImageForPokemonTimeSearch() {
        $margin_top = $this->image_height * 0.59375;
        if( $this->_hasAndroidNavigationBar() ) {
            $margin_top -= ($this->image_height * 0.075) * 0.3;
        }
        $this->image_time = imagecrop($this->image, ['x' => $this->image_width * 0.74574, 'y' => $margin_top, 'width' => $this->image_width * 0.1945, 'height' => $this->image_height * 0.0365]);
        $this->image_time_path =  get_stylesheet_directory() . '/captures/' . $this->filename . '-time.jpg';  
        imagefilter($this->image_time, IMG_FILTER_BRIGHTNESS, -130 );
        imagefilter($this->image_time, IMG_FILTER_GRAYSCALE );
        imagefilter($this->image_time, IMG_FILTER_CONTRAST, -70 );        
        imagejpeg($this->image_time, $this->image_time_path); 
        imagedestroy($this->image_time);        
    } 
    
    /**
     * 
     * @return type
     */
    private function _perform() {        
        $this->result->gym = $this->_getGym();
        $this->result->eggLevel = $this->_getEggLevel();
        $this->result->date = $this->_getTime();
        
        if( isset($this->image_pokemon_path) && !empty($this->image_pokemon_path) ) {
            $this->result->pokemon = $this->_getPokemon();
        }
    }
    
    /**
     * 
     */
    private function _close() {
        imagedestroy($this->image); 
        $time_elapsed_secs = microtime(true) - $this->start;
        if( $this->debug ) $this->_log('========== Fin du traitement '.$this->image_url.' ('.round($time_elapsed_secs, 3).'s) ==========');
    }
    
    /**
     * =========================================================================
     * EXTRACT DATA METHODS
     * =========================================================================
     */
    
    private function _getImgType() {
        if( $this->debug ) $this->_log('---------- Check if image is Raid Announce ----------');
        $image = imagecreatefromjpeg($this->image_path);
        
        $rgb = imagecolorsforindex($image, imagecolorat($image, $this->coordinates->forImgTypeEgg()->x, $this->coordinates->forImgTypeEgg()->y ));
        if( $this->debug ) $this->_log('Test pixel at x:'.$this->coordinates->forImgTypeEgg()->x.' & y:'.$this->coordinates->forImgTypeEgg()->y);
        if( $this->debug ) $this->_log('Result : R:'.$rgb['red'].' G:'.$rgb['green'].' B:'.$rgb['blue']);
        if( 
            ( $rgb['red'] > 230 && $rgb['red'] < 255 )
            && ( $rgb['green'] > 130 && $rgb['green'] < 145 ) 
            && ( $rgb['blue'] > 144 && $rgb['blue'] < 154 ) 
        ) {
            if( $this->debug ) $this->_log('Great ! Img seems to include an egg');
            return 'egg';
        } 
        
        /*
        //EGG step 1
        $rgb = imagecolorsforindex($image, imagecolorat($image, $this->image_width * 0.5, $this->image_height *0.1953 ));
        if( $this->debug ) $this->_log('Test pixel at x:'.$this->image_width * 0.5.' & y:'.$this->image_height *0.1953);
        if( $this->debug ) $this->_log('Result : R:'.$rgb['red'].' G:'.$rgb['green'].' B:'.$rgb['blue']);
        if( 
            ( $rgb['red'] > 245 && $rgb['red'] < 255 )
            && ( $rgb['green'] > 130 && $rgb['green'] < 140 ) 
            && ( $rgb['blue'] > 144 && $rgb['blue'] < 154 ) 
        ) {
            if( $this->debug ) $this->_log('Great ! Img seems to include an egg');
            return 'egg';
        } 
        
        //EGG step 2
        $rgb = imagecolorsforindex($image, imagecolorat($image, $this->image_width * 0.5, $this->image_height *0.21 ));
        if( $this->debug ) $this->_log('Test pixel at x:'.$this->image_width * 0.5.' & y:'.$this->image_height *0.21);
        if( $this->debug ) $this->_log('Result : R:'.$rgb['red'].' G:'.$rgb['green'].' B:'.$rgb['blue']);
        if( 
            ( $rgb['red'] > 245 && $rgb['red'] < 255 )
            && ( $rgb['green'] > 130 && $rgb['green'] < 140 ) 
            && ( $rgb['blue'] > 144 && $rgb['blue'] < 154 ) 
        ) {
            if( $this->debug ) $this->_log('Great ! Img seems to include an egg');
            return 'egg';
        }*/
        
        if( $this->debug ) $this->_log('IMG does not seem to be an egg. Trying to check if it includes a pokemon');
        $result = $this->_isPokemonImg();
        if( $result == true ) {
            if( $this->debug ) $this->_log('Great ! Img seems to include a pokemon');
            return 'pokemon';            
        }
        if( $this->debug ) $this->_log('Img does not seem to be a raid announce');
        $this->result->error = 'Img does not seem to be a raid announce';
        return false;        
    }
    
    /**
     * 
     * @deprecated since version 2018-05-27
     * @return boolean|string
     */
    private function _isRaidAnnounceImg() {
        if( $this->debug ) $this->_log('---------- Check if image is Raid Announce ----------');
        $image = imagecreatefromjpeg($this->image_basic_path);
        $rgb = imagecolorsforindex($image, imagecolorat($image, $this->image_width * 0.1, $this->image_height *0.7 ));
        if( $this->debug ) $this->_log('Test pixel at x:'.$this->image_width * 0.1.' & y:'.$this->image_height *0.7);
        if( 
            ( $rgb['red'] > 40 && $rgb['red'] < 85 )
            && ( $rgb['green'] > 95 && $rgb['green'] < 160 ) 
            && ( $rgb['blue'] > 55 && $rgb['blue'] < 90 ) 
        ) {
            if( $this->debug ) $this->_log('Img seems to be a raid announce');
            return 'egg';
        } 
        $result = $this->_isPokemonImg();
        if( $result == true ) {
            if( $this->debug ) $this->_log('Img seems to be a raid announce');
            return 'pokemon';            
        }
        if( $this->debug ) $this->_log('Img does not seem to be a raid announce');
        $this->result->error = 'Img does not seem to be a raid announce';
        return false;
    }
    
      
    /**
     * 
     * @return type
     */
    private function _getGym() {
        
        if( $this->debug ) $this->_log('---------- Gym Extraction ----------');
        
        //Step 1 - basic OCR
        if( $this->debug ) $this->_log('First attempt (config 1)' );
        $return = $this->tesseract->recognize( $this->image_gym_path, null, 7 );        
        if( $this->debug ) $this->_log('OCR value : ' . $return );
        if( $this->_looksValidGymName($return) ) {
            if( $this->debug ) $this->_log('Value looks valid gym name' );;
            if( $this->debug ) $this->_log('Looking for mathcing gym...');
            $result = $this->gymSearch->findGym($return, 75);
            if( $result ) {
                if( $this->debug ) $this->_log('Gym finded in database : ' . $result->getNameFr() );
                return $result;
            }
            if( $this->debug ) $this->_log('Nothing found in database :(' );
        } 
        if( $this->debug ) $this->_log('Value does not seem to be a correct gym name' );
        
        //Step 2
        if( $this->debug ) $this->_log('New attempt with other image settings (config 2)' );
        $image = imagecreatefromjpeg($this->image_gym_path);
        imagefilter($image, IMG_FILTER_EMBOSS );
        $this->image_gym_path2 = get_stylesheet_directory() . '/captures/' . $this->filename . '-gym-2.jpg';
        imagejpeg($image, $this->image_gym_path2); 
        imagedestroy($image);
        $return = $this->tesseract->recognize( $this->image_gym_path2, null, 7 );
        if( $this->debug ) $this->_log('OCR value : ' . $return );
        if( $this->_looksValidGymName($return) ) {
            if( $this->debug ) $this->_log('Value looks valid gym name' );;
            if( $this->debug ) $this->_log('Looking for mathcing gym...');
            $result = $this->gymSearch->findGym($return, 50);
            if( $result ) {
                if( $this->debug ) $this->_log('Gym finded in database : ' . $result->getNameFr() );
                return $result;
            }
            if( $this->debug ) $this->_log('Nothing found in database :(' );
        } 
        if( $this->debug ) $this->_log('Value does not seem to be a correct gym name' );
        
        //Step 3
        if( $this->debug ) $this->_log('New attempt with other image settings (config 3)' );
        $image = imagecreatefromjpeg($this->image_gym_path);
        imagefilter($image, IMG_FILTER_BRIGHTNESS, -130 );
        imagefilter($image, IMG_FILTER_GRAYSCALE );
        imagefilter($image, IMG_FILTER_CONTRAST, -70 );
        //imagefilter($image, IMG_FILTER_EMBOSS );
        $this->image_gym_path3 = get_stylesheet_directory() . '/captures/' . $this->filename . '-gym-3.jpg';
        imagejpeg($image, $this->image_gym_path3); 
        imagedestroy($image);
        $return = (new thiagoalessio\TesseractOCR\TesseractOCR($this->image_gym_path3))
            ->whitelist( $this->_getOcrWhiteList() )
            ->psm(7)
            ->run();
        if( $this->debug ) $this->_log('OCR value : ' . $return );
        if( $this->_looksValidGymName($return) ) {
            if( $this->debug ) $this->_log('Value looks valid gym name' );;
            if( $this->debug ) $this->_log('Looking for mathcing gym...');
            $result = $this->gymSearch->findGym($return, 30);
            if( $result ) {
                if( $this->debug ) $this->_log('Gym finded in database : ' . $result->getNameFr()  );
                return $result;
            }
            if( $this->debug ) $this->_log('Nothing found in database :(' );
        } else {
            if( $this->debug ) $this->_log('Value does not seem to be a correct gym name' ); 
        }
             
        
        //Step 4
        if( $this->debug ) $this->_log('New attempt with other image settings (config 4)' );
        $size = getimagesize($this->image_gym_path);
        $height = $size[1];
        $width = $size[0];
        $image = imagecreatefromjpeg($this->image_gym_path);
        $image_to_save = imagecreatefromjpeg($this->image_gym_path);
        $this->image_gym_path4 =  get_stylesheet_directory() . '/captures/' . $this->filename . '-gym-4.jpg';
        
        // start from the top-left pixel and keep looping until we have the desired effect
        for($y = 0;$y < $height;$y += 1) {    
            for($x = 0;$x < $width;$x += 1) {
            
                // get the color for current pixel
                $rgb = imagecolorsforindex($image, imagecolorat($image, $x, $y));

                // get the closest color from palette
                if( $rgb['red'] > 210 && $rgb['blue'] > 210 && $rgb['green'] > 210 ) {
                    $color = imagecolorallocate ( $image , $rgb['red'] , $rgb['green'] , $rgb['blue'] );
                    imagefilledrectangle($image, $x-1, $y-1, $x, $y, $color); 
                } else {
                    $color = imagecolorallocate ( $image , 0 , 0 , 0 );
                    imagefilledrectangle($image, $x-1, $y-1, $x, $y, $color);                   
                }                 
            }       
        }
        
        imagejpeg($image, $this->image_gym_path4); 
        imagedestroy($image);
        $return = (new thiagoalessio\TesseractOCR\TesseractOCR($this->image_gym_path4))
            ->whitelist( $this->_getOcrWhiteList() )
            ->psm(6)
            ->run();
        if( $this->debug ) $this->_log('OCR value : ' . $return );
        if( $this->_looksValidGymName($return) ) {
            if( $this->debug ) $this->_log('Value looks valid gym name' );;
            if( $this->debug ) $this->_log('Looking for mathcing gym...');
            $result = $this->gymSearch->findGym($return, 40);
            if( $result ) {
                if( $this->debug ) $this->_log('Gym finded in database : ' . $result->getNameFr() );
                return $result;
            }
            if( $this->debug ) $this->_log('Nothing found in database :(' );
        } 
        if( $this->debug ) $this->_log('Value does not seem to be a correct gym name' );  
        
        //Step 5
        if( $this->debug ) $this->_log('New attempt with other image settings (config 5)' );
        $size = getimagesize($this->image_gym_path);
        $height = $size[1];
        $width = $size[0];
        $image = imagecreatefromjpeg($this->image_gym_path);
        $image_to_save = imagecreatefromjpeg($this->image_gym_path);
        $this->image_gym_path5 =  get_stylesheet_directory() . '/captures/' . $this->filename . '-gym-5.jpg';
        
        // start from the top-left pixel and keep looping until we have the desired effect
        for($y = 0;$y < $height;$y += 1) {    
            for($x = 0;$x < $width;$x += 1) {
            
                // get the color for current pixel
                $rgb = imagecolorsforindex($image, imagecolorat($image, $x, $y));

                // get the closest color from palette
                if( $rgb['red'] > 150 && $rgb['blue'] > 150 && $rgb['green'] > 150 ) {
                    $color = imagecolorallocate ( $image , 255 , 255 , 255 );
                    imagefilledrectangle($image, $x-1, $y-1, $x, $y, $color); 
                } else {
                    $color = imagecolorallocate ( $image , 0 , 0 , 0 );
                    imagefilledrectangle($image, $x-1, $y-1, $x, $y, $color);                   
                }                 
            }       
        }
        imagefilter($image, IMG_FILTER_NEGATE  );     
        imagejpeg($image, $this->image_gym_path5); 
        imagedestroy($image);
        $return = (new thiagoalessio\TesseractOCR\TesseractOCR($this->image_gym_path5))
            ->whitelist( $this->_getOcrWhiteList() )
            ->psm(6)
            ->run();
        if( $this->debug ) $this->_log('OCR value : ' . $return );
        if( $this->_looksValidGymName($return) ) {
            if( $this->debug ) $this->_log('Value looks valid gym name' );;
            if( $this->debug ) $this->_log('Looking for mathcing gym...');
            $result = $this->gymSearch->findGym($return, 30);
            if( $result ) {
                if( $this->debug ) $this->_log('Gym finded in database : ' . $result->getNameFr() );
                return $result;
            }
            if( $this->debug ) $this->_log('Nothing found in database :(' );
        } 
        if( $this->debug ) $this->_log('Value does not seem to be a correct gym name' );
        
        return false;
    }
    
    /**
     * 
     * @return boolean
     */
    private function _getTime() {
        if( $this->debug ) $this->_log('---------- Date Extraction ----------');
        //$time = $this->tesseract->recognize( $this->image_time_path, null, 7 );
        
        $time = (new thiagoalessio\TesseractOCR\TesseractOCR($this->image_time_path))
            ->psm(7)
            ->run();

        if( $this->debug ) $this->_log( 'OCR Value : ' . $time );
        $time = str_replace('O', '0',$time);
        //$time = preg_replace('/[^0-9]/', '',$time);
        $time_ex = explode(':', $time); 
        //if( $this->debug ) $this->_log( 'Sanitized value : ' . $time );
        if( is_array($time_ex) && array_key_exists(1, $time_ex) ) {
            //$minutes = substr($time, 1, 2 );
            $minutes = $time_ex[1];
            if( $this->debug ) $this->_log( 'Minutes : ' . $minutes );
            $date = new DateTime();
            $date->setTimezone(new DateTimeZone('Europe/Paris'));
            if( $this->image_type == 'egg' ) {
                $date->modify('+'.$minutes.' minutes');
            } else {
                $minutes = 45 - $minutes;
                $date->modify('-'.$minutes.' minutes');
            }
            return $date->format('Y-m-d H:i:s');
        }   
        return false;
    }
    
    /**
     * 
     * @return boolean
     */
    private function _getPokemon() {
        if( $this->debug ) $this->_log('---------- Pokemon Extraction ----------');

        if( $this->debug ) $this->_log('First attempt (config 1)' );
        $image = imagecreatefromjpeg($this->image_pokemon_path);
        imagefilter($image, IMG_FILTER_GAUSSIAN_BLUR );
        imagefilter($image, IMG_FILTER_SELECTIVE_BLUR );
        imagefilter($image, IMG_FILTER_MEAN_REMOVAL );
        imagefilter($image, IMG_FILTER_SMOOTH, -6 );
        $this->image_pokemon_path1 = get_stylesheet_directory() . '/captures/' . $this->filename . '-pokemon-1.jpg';
        imagejpeg($image, $this->image_pokemon_path1); 
        imagedestroy($image);        
        //$ocr = $this->tesseract->recognize( $this->image_pokemon_path1, null, 7 );
        $ocr = (new thiagoalessio\TesseractOCR\TesseractOCR($this->image_pokemon_path1))
            ->whitelist( range('a','z'), 'é' )  
            //->lang('en')
            //->userWords( get_template_directory() . '/ocr/en.user-words')
            ->config('load_system_dawg', 'F')
            ->config('load_freq_dawg', 'F')
            ->config('user_words_file', get_template_directory() . '/ocr/en.user-words' )
            ->config('language_model_penalty_non_dict_word', 0.9)
            ->psm(2)
            ->run();
        $ocr = sanitize_title($ocr);       
        if( $this->debug ) $this->_log('OCR value : '.$ocr);
        if( $this->debug ) $this->_log('Looking for pokemon in DataBase...');
        $result = $this->_searchMatchingPokemon($ocr, 1);
        if( $result['pokemon'] && $result['probability'] > 75 ) {
            if( $this->debug ) $this->_log('Pokemon might be '.$result['pokemon']->getNameFr().' ('.$result['probability'].'% probability)');
            $this->result->eggLevel = $result['pokemon']->getRaidLevel();
            return $result['pokemon'];
        }
        if( $this->debug ) $this->_log('Nothing found in database :(');
      
        if( $this->debug ) $this->_log('New attempt with other image settings (config 2)' );
        $size = getimagesize($this->image_pokemon_path);
        $height = $size[1];
        $width = $size[0];
        $image = imagecreatefromjpeg($this->image_pokemon_path);
        $image_to_save = imagecreatefromjpeg($this->image_pokemon_path);
        $this->image_pokemon_path2 =  get_stylesheet_directory() . '/captures/' . $this->filename . '-pokemon-2.jpg';
        
        // start from the top-left pixel and keep looping until we have the desired effect
        for($y = 0;$y < $height;$y += 1) {    
            for($x = 0;$x < $width;$x += 1) {
            
                // get the color for current pixel
                $rgb = imagecolorsforindex($image, imagecolorat($image, $x, $y));

                // get the closest color from palette
                if( $rgb['red'] > 245 && $rgb['blue'] > 245 && $rgb['green'] > 245 ) {
                    $color = imagecolorallocate ( $image , 255 , 255 , 255 );
                    imagefilledrectangle($image, $x-1, $y-1, $x, $y, $color); 
                } else {
                    $color = imagecolorallocate ( $image , 0 , 0 , 0 );
                    imagefilledrectangle($image, $x-1, $y-1, $x, $y, $color);                   
                }                 
            }       
        }
        imagefilter($image, IMG_FILTER_NEGATE  );
        imagejpeg($image, $this->image_pokemon_path2); 
        imagedestroy($image);

        $ocr = (new thiagoalessio\TesseractOCR\TesseractOCR($this->image_pokemon_path2))
            ->whitelist( range('a','z'), 'é' )  
            //->lang('en')
            //->userWords( get_template_directory() . '/ocr/en.user-words')
            ->config('load_system_dawg', 'F')
            ->config('load_freq_dawg', 'F')
            ->config('user_words_file', get_template_directory() . '/ocr/en.user-words' )
            ->config('language_model_penalty_non_dict_word', 0.9)
            ->psm(7)
            ->run();
        
        if( $this->debug ) $this->_log('OCR value : '.$ocr);
        if( $this->debug ) $this->_log('Looking for pokemon in DataBase...');
        $result = $this->_searchMatchingPokemon($ocr, 2);
        if( $result['pokemon'] && $result['probability'] > 50 ) {
            if( $this->debug ) $this->_log('Pokemon might be '.$result['pokemon']->getNameFr().' ('.$result['probability'].'% probability)');
            $this->result->eggLevel = $result['pokemon']->getRaidLevel();
            return $result['pokemon'];
        }
        if( $this->debug ) $this->_log('Nothing found in database :(');
        return false;

    }
    
    /**
     * 
     * @return int
     */
    private function _getEggLevel() {
        $egg_level = 0;
        if( $this->debug ) $this->_log('---------- Egg level Extraction ----------');
        foreach( array(5,4,3,2,1) as $egglevel ) {
            $count_egg_level = 0;
            foreach( $this->coordinates->forEggLevel() as $coor ) {
                if( !in_array($egglevel, $coor->lvl) ) {
                    continue;
                } 
                if( $this->debug ) $this->_log('Test pixel at x:'.$coor->x.' & y:'.$coor->y);
                $rgb = imagecolorsforindex($this->image, imagecolorat($this->image, $coor->x, $coor->y ));
                if( $this->_isEgglevelColor($rgb) ) {
                    $count_egg_level += 1;
                    if( $this->debug ) $this->_log('Pixel matches');
                } else {
                    if( $this->debug ) $this->_log('Pixel does not match');
                }            
            }  
            if( $this->debug ) $this->_log($count_egg_level . ' matching pixels, '.$egglevel.' expected');
            if( $egglevel === $count_egg_level ) {
                return $egglevel;
            }
        }
        return false;
    }
    
    private function _getEggLevelForActiveRaid() {
        if( $this->debug ) $this->_log('---------- Egg level Extraction ----------');
        foreach( array(5,4,3,2,1) as $egglevel ) {
            $count_egg_level = 0;
            foreach( $this->_getEggLevelCoordinatesForActiveRaid() as $coor ) {
                if( !in_array($egglevel, $coor[2]) ) {
                    continue;
                }                
                if( $this->debug ) $this->_log('Test pixel at x:'.$coor[0].' & y:'.$coor[1]);
                $rgb = imagecolorsforindex($this->image, imagecolorat($this->image, $coor[0], $coor[1] ));
                if( $this->_isEgglevelColor($rgb) ) {
                    $count_egg_level += 1;
                    if( $this->debug ) $this->_log('Pixel matches');
                } else {
                    if( $this->debug ) $this->_log('Pixel does not match');
                }            
            }
            if( $this->debug ) $this->_log($count_egg_level . ' matching pixels, '.$egglevel.' expected');
            if( $egglevel === $count_egg_level ) {
                return $egglevel;
            }
        }
        
        return false;
    }
    
    /**
     * =========================================================================
     * HELPER METHODS
     * =========================================================================
     */
    
    /**
     * 
     * @return type
     */
    private function _getEggLevelCoordinates() {
        
        $margin_top = $this->image_height * 0.296875;
        if( $this->debug ) $this->_log( 'Margin top : ' . $margin_top );
        
        if( $this->_hasAndroidNavigationBar() ) {            
            $margin_top -= ($this->image_height * 0.075) * 0.25;
            if( $this->debug ) $this->_log('Img has Android navigation bar');
            if( $this->debug ) $this->_log( 'Ajusted margin top : ' . $margin_top );
        }
        
        if( $this->_getImageRatio() >= 0.75 ) {
            return array(
                array( $this->image_width * ( 0.3037 + 0.0496 ), $margin_top ), //5
                array( $this->image_width * ( 0.3611 + 0.0496 ), $margin_top ), //4
                array( $this->image_width * ( 0.4267 + 0.0248 ), $margin_top ), //5, 3
                array( $this->image_width * ( 0.4509 + 0.0248 ), $margin_top ), //4, 2
                array( $this->image_width * 0.4990, $margin_top ), //5, 3, 1
                array( $this->image_width * ( 0.5481 - 0.0248 ), $margin_top ), //4, 2
                array( $this->image_width * ( 0.5972 - 0.0248 ), $margin_top ), //5, 3
                array( $this->image_width * ( 0.6388 - 0.0496 ), $margin_top ), //4
                array( $this->image_width * ( 0.6954 - 0.0496 ), $margin_top ), //5
            );    
        }
               
        return array(
            array( $this->image_width * 0.3037, $margin_top ), //5
            array( $this->image_width * 0.3611, $margin_top ), //4
            array( $this->image_width * 0.4019, $margin_top ), //5, 3
            array( $this->image_width * 0.4509, $margin_top ), //4, 2
            array( $this->image_width * 0.4990, $margin_top ), //5, 3, 1
            array( $this->image_width * 0.5481, $margin_top ), //4, 2
            array( $this->image_width * 0.5972, $margin_top ), //5, 3
            array( $this->image_width * 0.6388, $margin_top ), //4
            array( $this->image_width * 0.6954, $margin_top ), //5
        );

    }
    
    private function _getEggLevelCoordinatesForActiveRaid() {
        
        $margin_top = $this->image_height * 0.146875;
        if( $this->debug ) $this->_log( 'Margin top : ' . $margin_top );
               
        $return = array(
            array( $this->image_width * 0.3722, $margin_top, array(5) ), //5
            array( $this->image_width * 0.4046, $margin_top, array(4) ), //4
            array( $this->image_width * 0.4361, $margin_top, array(5,3) ), //5, 3
            array( $this->image_width * 0.4685, $margin_top, array(4,2) ), //4, 2
            array( $this->image_width * 0.4990, $margin_top, array(5,3,1) ), //5, 3, 1
            array( $this->image_width * 0.5314, $margin_top, array(4,2) ), //4, 2
            array( $this->image_width * 0.5629, $margin_top, array(5,3) ), //5, 3
            array( $this->image_width * 0.5944, $margin_top, array(4) ), //4
            array( $this->image_width * 0.6268, $margin_top, array(5) ), //5
        );
        return $return;
    }
    
    /**
     * 
     * @param type $rgb
     * @return boolean
     */
    private function _isEgglevelColor( $rgb ) {
        if( $rgb['red'] > 233 && $rgb['green'] > 233 && $rgb['blue'] > 233 ) {
            return true;
        }
        return false;        
    }
    
    /**
     * 
     * @param type $string
     * @return type
     */
    private function _sanitizeTime( $string ) {
        $string = str_replace(array(' ', '('), '', $string);
        return $string;
    } 
    
    /**
     * 
     * @param type $string
     * @return boolean
     */
    private function _looksValidGymName( $string ) {
        if( empty( $string ) ) return false;
        if( strlen($string) < 4 ) return false;
        return true;
    }

    
    /**
     * 
     * @return type
     */
    private function _getImageRatio() {
        return $this->image_width / $this->image_height;
    }
    
    
    /**
     * 
     * @return boolean
     */
    private function _hasAndroidNavigationBar() {
        $rgb = imagecolorsforindex($this->image, imagecolorat($this->image, $this->image_width - 1, $this->image_height - 1 ));
        if( $rgb['red'] == 0 && $rgb['green'] == 0 && $rgb['blue'] == 0 ) {
            return true;
        }        
        return false;
    }    
    
    
    /**
     * 
     * @param type $text
     * @return boolean|\POGO_gym
     */
    private function _findExstingGym( $text ) {
        foreach (POGO_query::getGyms() as $gym) {
            foreach( $gym->getSearhPatterns() as $pattern ) {
                if( strstr($text, $pattern) ) {
                    return $gym; 
                }
            }
        }
        return false;
    }
    
    
    /**
     * 
     * @return string
     */
    private function _getOcrWhiteList() {
        return 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMEOPQRSTUVWXYZ;:!-éèêà';
    }
    
    
    /**
     * 
     * @return boolean
     */
    private function _isPokemonImg() {
        //Premier check
        if( $this->debug ) $this->_log('Test pixel at x:'.$this->image_width * 0.92592.' & y:'.$this->image_height* 0.611979);
        $rgb = imagecolorsforindex($this->image, imagecolorat($this->image, $this->image_width * 0.92592 , $this->image_height* 0.611979 ));
        if( $this->debug ) $this->_log('Result : R:'.$rgb['red'].' G:'.$rgb['green'].' B:'.$rgb['blue']);
        if( 
            ( $rgb['red'] >= 250 && $rgb['red'] <= 256 )
            && ( $rgb['green'] >= 116 && $rgb['green'] <= 124 ) 
            && ( $rgb['blue'] >= 52 && $rgb['blue'] <= 58 ) 
        ) {
            return true;
        }
        
        //2ème check
        if( $this->debug ) $this->_log('Test pixel at x:'.$this->image_width * 0.91.' & y:'.$this->image_height* 0.611979);
        $rgb = imagecolorsforindex($this->image, imagecolorat($this->image, $this->image_width * 0.91 , $this->image_height* 0.611979 ));
        if( $this->debug ) $this->_log('Result : R:'.$rgb['red'].' G:'.$rgb['green'].' B:'.$rgb['blue']);
        if( 
            ( $rgb['red'] >= 250 && $rgb['red'] <= 256 )
            && ( $rgb['green'] >= 116 && $rgb['green'] <= 124 ) 
            && ( $rgb['blue'] >= 52 && $rgb['blue'] <= 58 ) 
        ) {
            return true;
        }
        //if( $this->debug ) $this->_log('Img seems to include an egg');
        return false;        
    }
    
    
    /**
     * 
     * @param type $pokemon
     * @return type
     */
    private function _searchMatchingPokemon($pokemon, $num_test ) {

        $match_engine = new POGO_pokemonMatchEngine( $pokemon, $this->result->eggLevel, $num_test );
        $match_engine->perform();

        return array(
            'pokemon' => $match_engine->getBestResult()->pokemon,
            'probability' => round(90)
        );        

    }
    
    private function getFirstPixel() {
        $size = getimagesize($this->image_path);
        $height = $size[1];
        $width = $size[0];
        $image = imagecreatefromjpeg($this->image_path);
        //$image_to_save = imagecreatefromjpeg($this->image_pokemon_path);
        //$this->image_pokemon_path2 =  get_stylesheet_directory() . '/captures/' . $this->filename . '-pokemon-2.jpg';
        
        // start from the top-left pixel and keep looping until we have the desired effect
        for($y = 0;$y < $height;$y += 1) {    
            
            //Get the color of the pixel
            $rgb = imagecolorsforindex($image, imagecolorat($image, 2, $y));
            // get the closest color from palette
            if( $rgb['red'] == 0 && $rgb['blue'] == 0 & $rgb['green'] == 0 ) {
                continue;
            }
            if( $rgb['red'] == 36 && $rgb['green'] == 132 & $rgb['blue'] == 232 ) {
                continue;
            }
            return $y;
        }

        return 0;
    }
    
    private function getLastPixel() {
        $size = getimagesize($this->image_path);
        $height = $size[1];
        $width = $size[0];
        $image = imagecreatefromjpeg($this->image_path);
        //$image_to_save = imagecreatefromjpeg($this->image_pokemon_path);
        //$this->image_pokemon_path2 =  get_stylesheet_directory() . '/captures/' . $this->filename . '-pokemon-2.jpg';
        
        // start from the top-left pixel and keep looping until we have the desired effect
        for($y = $height;$y > 0;$y -= 1) {    
            
            //Get the color of the pixel
            $rgb = imagecolorsforindex($image, imagecolorat($image, 2, $y - 1));

            // get the closest color from palette
            if( $rgb['red'] != 0 && $rgb['blue'] != 0 && $rgb['green'] != 0 ) { 
                return $y;                  
            }                       
        }

        return 0;
    }
    
    private function cropImage() {
        $image = imagecreatefromjpeg($this->image_path);
        $image2 = imagecrop($image, ['x' => 0, 'y' => $this->getFirstPixel(), 'width' => $this->image_width, 'height' => $this->getLastPixel() - $this->getFirstPixel()]);
        if ($image2 !== FALSE) {
            imagejpeg($image2, $this->image_path);
            imagedestroy($image2);             
        }       
    }
    
    

}