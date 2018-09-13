<?php get_header(); ?>

<div class="page-content-full container mt-60 mb-60">
    <div class="row">

        <div class="col-sm">
            <div id="content" role="main">                        
                <?php
                $raid = new POGO_raid(2542);
                echo '<pre>';
                //var_dump( $raid->getMatchingConnectors() );
                echo '</pre>';
                //var_dump($GymSearch->getAllIdentifiers());
                ?>
                <?php dynamic_sidebar('sidebar-widget-area'); ?>
            </div><!-- /#content -->
        </div>

        <?php //get_sidebar(); ?>

    </div><!-- /.row -->
</div><!-- /.container-responsive -->

<?php get_footer(); ?>
