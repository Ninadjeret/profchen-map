<?php
class POGO_admin_connector {

    function __construct() {
        $this->post_type = 'connector';
        add_action( 'init', array( $this, 'post_type_register' ), 0 );
        add_action( 'acf/save_post', array( $this, 'frontSavePost' ), 30, 1);
    }

    /**
     *
     */
    function post_type_register() {

        $labels = array(
            'name'                => __( 'Connecteurs', 'Post Type General Name', 'notifications-center' ),
            'singular_name'       => __( 'Connecteur', 'Post Type Singular Name', 'notifications-center' ),
            'menu_name'           => __( 'Connecteurs', 'notifications-center' ),
            'all_items'           => __( 'Tous les connecteurs', 'notifications-center' ),
            'view_item'           => __( 'Voir le connecteur', 'notifications-center' ),
            'add_new_item'        => __( 'Nouveau connecteur', 'notifications-center' ),
            'add_new'             => __( 'Nouveau', 'notifications-center' ),
            'edit_item'           => __( 'Modifier', 'notifications-center' ),
            'update_item'         => __( 'Mettre à jour', 'notifications-center' ),
            'search_items'        => __( 'Rechercher', 'notifications-center' ),
            'not_found'           => __( 'Aucun résultat', 'notifications-center' ),
            'not_found_in_trash'  => __( 'Aucun résultat', 'notifications-center' ),
        );
        $args = array(
                'label'               => __( 'cpt_connecteur', 'notifications-center' ),
                'description'         => __( 'cpt_connecteur', 'notifications-center' ),
                'labels'              => $labels,
                'supports'            => array( 'title', 'comments', 'author', 'revisions', 'custom-fields', 'thumbnail' ),
                'hierarchical'        => false,
                'public'              => true,
                'show_ui'             => true,
                'show_in_menu'        => true,
                'show_in_nav_menus'   => true,
                'show_in_admin_bar'   => true,
                'menu_position'       => 5,
                'menu_icon'           => 'dashicons-networking',
                'can_export'          => true,
                'has_archive'         => false,
                'exclude_from_search' => false,
                'publicly_queryable'  => true,
                'capability_type'     => 'post',
        );
        register_post_type( $this->post_type, $args );

    }
    
    public function frontSavePost( $post_id ) {
        if( is_admin() ) return;
        if( get_post_type( $post_id ) != $this->post_type ) return;
        if( !isset( $_GET['communityId'] ) || empty( $_GET['communityId'] ) ) return;
        
        $communityId = $_GET['communityId'];
        $connector = new POGO_connector( $post_id );
        $user = new POGO_user( get_current_user_id() );
        
        if( $connector->getCommunity() ) return;
        if( !$user->canEditCommunity($communityId) ) return;
        
        update_field('connector_community', $communityId, $post_id);
    }

}

new POGO_admin_connector();
