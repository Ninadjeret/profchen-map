<?php
/*
 * Template Name: App vue.js
 */
get_header(); ?>
<div class="page-content-full container">
    <div id="app">
      {{ message }}
    </div>
</div>
<?php get_footer(); ?>

<script>
new Vue({
  el: '#app',
  data: {
    message: 'Hello Vue.js!'
  }
})
</script>

