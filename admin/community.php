<?php
class POGO_admin_community {

    function __construct() {
        $this->post_type = 'community';
        add_action( 'init', array( $this, 'post_type_register' ), 0 );
        //add_action( 'add_meta_boxes', array( $this, 'metaboxes') );
    }

    /**
     *
     */
    function post_type_register() {

        $labels = array(
            'name'                => __( 'Communautés', 'Post Type General Name', 'notifications-center' ),
            'singular_name'       => __( 'Communauté', 'Post Type Singular Name', 'notifications-center' ),
            'menu_name'           => __( 'Communautés', 'notifications-center' ),
            'all_items'           => __( 'Toutes les communautés', 'notifications-center' ),
            'view_item'           => __( 'Voir la communauté', 'notifications-center' ),
            'add_new_item'        => __( 'Nouvelle communauté', 'notifications-center' ),
            'add_new'             => __( 'Ajouter', 'notifications-center' ),
            'edit_item'           => __( 'Modifier', 'notifications-center' ),
            'update_item'         => __( 'Mettre à jour', 'notifications-center' ),
            'search_items'        => __( 'Rechercher', 'notifications-center' ),
            'not_found'           => __( 'Aucun résultat', 'notifications-center' ),
            'not_found_in_trash'  => __( 'Aucun résultat', 'notifications-center' ),
        );
        $args = array(
                'label'               => __( 'cpt_community', 'notifications-center' ),
                'description'         => __( 'cpt_community', 'notifications-center' ),
                'labels'              => $labels,
                'supports'            => array( 'title', 'comments', 'author', 'revisions', 'custom-fields', 'thumbnail' ),
                'hierarchical'        => false,
                'public'              => true,
                'show_ui'             => true,
                'show_in_menu'        => true,
                'show_in_nav_menus'   => true,
                'show_in_admin_bar'   => true,
                'menu_position'       => 5,
                'menu_icon'           => 'dashicons-groups',
                'can_export'          => true,
                'has_archive'         => false,
                'exclude_from_search' => false,
                'publicly_queryable'  => true,
                'capability_type'     => 'post',
        );
        register_post_type( $this->post_type, $args );

    }


}

new POGO_admin_community();
