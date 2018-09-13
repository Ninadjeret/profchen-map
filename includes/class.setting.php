<?php

class POGO_settings {
    
    function __construct() {
        ;
    }
    
    function DisplaySetting( $type, $args ) {
        if( $type == 'toggle' ) {
            $this->displayToggleSetting( $args );
        }
    }
    
    public static function displayToggleSetting( $args ) {
        $val = get_field( $args['id'], 'user_'.get_current_user_id() );
        ?>
        <div class="setting__wrapper toggle"> 
            <div class="setting_label">
                <p class="setting__titre"><?php echo $args['title']; ?></p>
                <p class="setting__desc"><?php echo $args['description']; ?></p>
            </div>
            <div class="setting__value">
                <label class="mdl-switch mdl-js-switch mdl-js-ripple-effect <?php if( $val ) echo 'is-checked'; ?>" for="<?php echo $args['id']; ?>">
                    <span class="mdl-switch__label"></span>
                    <input data-type="toggle" data-id="<?php echo $args['id']; ?>" type="checkbox" id="<?php echo $args['id']; ?>" class="mdl-switch__input" <?php if( $val ) echo 'checked'; ?>>
                </label> 
            </div>
        </div>
        <?php
    }
    
    public static function displaySelectSetting( $args ) {
        $val = get_field( $args['id'], 'user_'.get_current_user_id() );
        ?>
        <div class="setting__wrapper select"> 
            <div class="setting_label">
                <p class="setting__titre"><?php echo $args['title']; ?></p>
                <p class="setting__desc"><?php echo $args['description']; ?></p>
            </div>
            <div class="setting__value">
                <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                    <select class="mdl-textfield__input" id="<?php echo $args['id']; ?>" data-id="<?php echo $args['id']; ?>" name="<?php echo $args['id']; ?>">
                        <?php foreach( $args['choices'] as $value => $label ) { ?>
                            <option value="<?php echo $value; ?>" <?php if($value == $val) echo 'selected'; ?>><?php echo $label; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
        </div>
        <?php
    }
    
    public static function saveToggleSetting() {
        if( !get_current_user_id() ) return false;
        
        $user_id = get_current_user_id();
        $setting_id = $_POST['settingId'];
        $setting_val = $_POST['settingVal'];
        
        update_field($setting_id, $setting_val, 'user_'.$user_id);
        return true;       
    }
    
    public static function saveSelectSetting() {
        if( !get_current_user_id() ) return false;
        
        $user_id = get_current_user_id();
        $setting_id = $_POST['settingId'];
        $setting_val = $_POST['settingVal'];
        
        error_log('--- Enregistrement ---');
        error_log($user_id);
        error_log($setting_val);
        
        update_field($setting_id, $setting_val, 'user_'.$user_id);
        return true;       
    }
    
    public static function getUserSettings() {
        if( !get_current_user_id() ) return false;
        
        $user_id = get_current_user_id();
        $user = new POGO_user($user_id);
        $mapHideEmpty = ( get_field('mapHideEmpty', 'user_'.$user_id) ) ? true : false;
        $mapDefaultPosition = ( get_field('mapDefaultPosition', 'user_'.$user_id) ) ? get_field('mapDefaultPosition', 'user_'.$user_id) : 'discord_vern';
        $role = ($user->getAdminCommunities()) ? 'communityAdmin' : 'user' ; 
        
        $settings = array(
            'role'                  => $role,
            'mapHideEmpty'          => $mapHideEmpty,
            'mapDefaultPosition'    => $mapDefaultPosition,
        );
        echo json_encode($settings);
        die();
        
    }
    
}
