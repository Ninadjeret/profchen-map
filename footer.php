  </main>
</div>

<div id="message"></div>

<?php include( 'templates/parts/modal.php' ); ?>

<?php wp_footer(); ?>
<?php if( !is_admin() ) { ?>
<script>
  if ('serviceWorker' in navigator) {
    console.log("Will the service worker register?");
    navigator.serviceWorker.register('<?php echo home_url(); ?>/service-worker.js')
      .then(function(reg){
        console.log("Yes, it did.");
      }).catch(function(err) {
        console.log("No it didn't. This happened: ", err)
      });
  }
</script>
<?php } ?>
</body>
</html>
