<h3>Your Site Caches</h3>
<script>
    function selectAllCaches(el){
        const checkboxes=document.getElementsByClassName('caches');
        Array.prototype.filter.call(checkboxes, function(cache){
            if(el.checked)
                cache.setAttribute('checked',el.checked);
            else 
                cache.removeAttribute('checked');
            return cache;
        })
    }
</script>
<form method="post">
<?php wp_nonce_field('mcw_caches_update');?>
<ul>
    <li><input type="checkbox" value="select_all" onclick="selectAllCaches(this)"/> <strong style="font-size:1.2em;">Select All Caches</strong></li>
<?php
    $caches = get_option(MCW_CACHE_OPTION_KEY,$default = [
        'last_update'=>null,
        'caches'=>[]
    ]);
    foreach ($caches['caches'] as $key => $cache):?>
    <li><input type="checkbox" name="mcw_caches[<?php echo $key;?>]" class="caches" value="<?php echo $key;?>"/> <?php echo $cache['url'];?></li>
    <?php endforeach;?>
</ul>
<?php submit_button('Clear Selected Caches');?>
</form>