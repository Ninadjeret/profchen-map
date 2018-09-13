<?php
class POGO_admin_raid {

    function __construct() {
        $this->post_type = 'raid';
        add_action( 'init', array( $this, 'post_type_register' ), 0 );
    }

    /**
     *
     */
    function post_type_register() {

        $labels = array(
            'name'                => __( 'Raids', 'Post Type General Name', 'notifications-center' ),
            'singular_name'       => __( 'Raid', 'Post Type Singular Name', 'notifications-center' ),
            'menu_name'           => __( 'Raids', 'notifications-center' ),
            'all_items'           => __( 'Tous les raids', 'notifications-center' ),
            'view_item'           => __( 'Voir le raid', 'notifications-center' ),
            'add_new_item'        => __( 'Nouveau raid', 'notifications-center' ),
            'add_new'             => __( 'Nouveau', 'notifications-center' ),
            'edit_item'           => __( 'Modifier', 'notifications-center' ),
            'update_item'         => __( 'Mettre à jour', 'notifications-center' ),
            'search_items'        => __( 'Rechercher', 'notifications-center' ),
            'not_found'           => __( 'Aucun résultat', 'notifications-center' ),
            'not_found_in_trash'  => __( 'Aucun résultat', 'notifications-center' ),
        );
        $args = array(
                'label'               => __( 'cpt_raid', 'notifications-center' ),
                'description'         => __( 'cpt_raid', 'notifications-center' ),
                'labels'              => $labels,
                'supports'            => array( 'title', 'comments', 'author', 'revisions', 'custom-fields', 'thumbnail' ),
                'hierarchical'        => false,
                'public'              => true,
                'show_ui'             => true,
                'show_in_menu'        => true,
                'show_in_nav_menus'   => true,
                'show_in_admin_bar'   => true,
                'menu_position'       => 5,
                'menu_icon'           => 'dashicons-share-alt',
                'can_export'          => true,
                'has_archive'         => false,
                'exclude_from_search' => false,
                'publicly_queryable'  => true,
                'capability_type'     => 'post',
        );
        register_post_type( $this->post_type, $args );

    }

}

new POGO_admin_raid();
