<?php
class POGO_admin_pokemon {

    function __construct() {
        $this->post_type = 'pokemon';
        add_action( 'init', array( $this, 'post_type_register' ), 0 );
        add_action( 'add_meta_boxes', array( $this, 'metaboxes') );
    }

    /**
     *
     */
    function post_type_register() {

        $labels = array(
            'name'                => __( 'Pokémon', 'Post Type General Name', 'notifications-center' ),
            'singular_name'       => __( 'Pokémon', 'Post Type Singular Name', 'notifications-center' ),
            'menu_name'           => __( 'Pokémon', 'notifications-center' ),
            'all_items'           => __( 'Tous les Pokémon', 'notifications-center' ),
            'view_item'           => __( 'Voir le Pokémon', 'notifications-center' ),
            'add_new_item'        => __( 'Nouveau Pokémon', 'notifications-center' ),
            'add_new'             => __( 'Nouveau Pokémon', 'notifications-center' ),
            'edit_item'           => __( 'Modifier', 'notifications-center' ),
            'update_item'         => __( 'Mettre à jour', 'notifications-center' ),
            'search_items'        => __( 'Rechercher', 'notifications-center' ),
            'not_found'           => __( 'Aucun résultat', 'notifications-center' ),
            'not_found_in_trash'  => __( 'Aucun résultat', 'notifications-center' ),
        );
        $args = array(
                'label'               => __( 'cpt_pokemon', 'notifications-center' ),
                'description'         => __( 'cpt_pokemon', 'notifications-center' ),
                'labels'              => $labels,
                'supports'            => array( 'title', 'comments', 'page-attributes', 'author', 'revisions', 'custom-fields', 'thumbnail' ),
                'hierarchical'        => true,
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

    /**
     * Enregistrement des métabox custom
     */
    function metaboxes() {
        add_meta_box( 'pokemon-raidlvl', 'Niveau de boss', array( $this, 'metabox_callback_raidlvl' ), $this->post_type, 'side' );
    }

    /**
     * Callback des métabox
     * @param type $post
     */
    function metabox_callback_raidlvl( $post ) {
        $pokemon = new POGO_pokemon( get_the_ID() );
        if( $pokemon ) {
            /*echo '<strong>Level 15</strong>';
            echo '<div>Max : '.$pokemon->getCp( 15, 15, 15, 15 ).' & min : '.$pokemon->getCp( 15, 10, 10, 10 ).'</div>';
            echo '<hr>';
            echo '<strong>Level 20</strong>';
            echo '<div>Max : '.$pokemon->getCp( 20, 15, 15, 15 ).' & min : '.$pokemon->getCp( 20, 10, 10, 10 ).'</div>';
            echo '<hr>';
            echo '<strong>Level 25</strong>';
            echo '<div>Max : '.$pokemon->getCp( 25, 15, 15, 15 ).' & min : '.$pokemon->getCp( 25, 10, 10, 10 ).'</div>';
            echo '<hr>'; */
            $attack_range = range(10, 15);
            $defense_range = range(10, 15);
            $stamina_range = range(10, 15);
            $return_array = array();
            
            foreach($attack_range as $attack) {
                foreach($defense_range as $defense) {
                    foreach($stamina_range as $stamina) {
                        $return_array[] = array(
                          'attack'    => $attack,
                          'defense'   => $defense,
                          'stamina'   => $stamina,
                          'lvl20'     => $pokemon->getCp( 20, $attack, $defense, $stamina ),
                          'lvl25'     => $pokemon->getCp( 25, $attack, $defense, $stamina ),
                          'lvl40'     => $pokemon->getCp( 40, $attack, $defense, $stamina ),
                        );
                    }            
                }              
            }
            ?>
            
            <table>
                <thead>
                    <tr>
                        <td>Lvl 20</td>
                        <td>Lvl 25</td>
                        <td>Attaque</td>
                        <td>Défense</td>
                        <td>Endurance</td>
                        <td>Lvl 40</td>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($return_array as $value) { ?>
                    <tr>
                        <td><?php echo $value['lvl20']; ?></td>
                        <td><?php echo $value['lvl25']; ?></td>
                        <td><?php echo $value['attack']; ?></td>
                        <td><?php echo $value['defense']; ?></td>
                        <td><?php echo $value['stamina']; ?></td>
                        <td><?php echo $value['lvl40']; ?></td>
                    </tr>                    
                    <?php } ?>
                </tbody>
            </table>
            
            <?php            
        }
    }

}

new POGO_admin_pokemon();
