<?php
//$raid
?>
<div class="raid__wrapper">
    <div class="raid__img">
        <?php if( $raid->getPokemon() ) { ?>
            <img src="https://assets.profchen.fr/img/pokemon/pokemon_icon_<?php echo $raid->getPokemon()->pokedexId; ?>_00.png" />
        <?php } else { ?>
            <img src="https://assets.profchen.fr/img/eggs/egg_<?php echo $raid->getEggLevel(); ?>.png" />
        <?php } ?>        
    </div>
    <div class="raid__content">
            <h3>
                <?php echo $raid->getEggLevel().'T de '.$raid->getStartTime()->format('H\hi')." à ".$raid->getEndTime()->format('H\hi'); ?>
                <span class="raid__timer <?php echo $raid->getStatus(); ?>" data-start="<?php echo $raid->getStartTime()->format('Y-m-d H:i:s'); ?>" data-end="<?php echo $raid->getEndTime()->format('Y-m-d H:i:s'); ?>">
                </span>
            </h3>
            <div class="raid__gym">
                <img src="https://d30y9cdsu7xlg0.cloudfront.net/png/4096-200.png" />
                <?php echo $raid->getGym()->getCity().' - '.$raid->getGym()->getNameFr(); ?>
            </div>
    </div>
</div>

