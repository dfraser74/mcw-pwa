<h3>Set Your Offline Page</h3>
<article class="description">
<p>You can select page that you want to show when user on offline mode. You need to create a page first before able to set it as offline page.</p>
<p>If you want to customize your offline page, you can create your custom template, then create page from template, then set it here as offline page.</p>
</article>

<div id="preaches-container">
<form method="POST">
<?php wp_nonce_field('mcw_offline');?>
<p>Show this page on offline mode <?php wp_dropdown_pages( [
    'name'=>'mcw_offline_page',
    'show_option_none'=>'No offline page',
    'selected'=>MCW_PWA_Service_Worker::instance()->getOfflinePage()
    ] ); ?></p>
<?php submit_button('Set Offline Page');?>
</form>
</div>