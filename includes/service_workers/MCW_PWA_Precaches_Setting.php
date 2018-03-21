<h3>Your Statics Assets To Precaches</h3>
<script>
    function selectAllCaches(el){
        const checkboxes=document.getElementsByClassName('precaches');
        Array.prototype.filter.call(checkboxes, function(cache){
            if(el.checked)
                cache.setAttribute('checked',el.checked);
            else 
                cache.removeAttribute('checked');
            return cache;
        })
    }

    function addPrecache(){
        const newPrecache=document.getElementById('new-precache');
        const precaches=document.getElementById('precaches-list');
        const list = document.createElement('li');
        list.innerHTML = `<input type="checkbox" name="mcw_precaches[]" class="precaches" value="${newPrecache.value}" checked/> ${newPrecache.value}`.trim();
        precaches.append(list);
        newPrecache.value='';
    }
</script>
<div id="add-precache">
    <input type="text" id="new-precache" size="40"/> <button class="button-primary" onclick="addPrecache()">Add Precache</button>
</div>
<form method="post">
<?php wp_nonce_field('mcw_precaches_update');?>
<ul id="precaches-list">
    <li><input type="checkbox" value="select_all" onclick="selectAllCaches(this)"/> <strong style="font-size:1.2em;">Select All Preaches</strong></li>
<?php
    $precaches = MCW_PWA_Service_Worker::instance()->getPrecaches();
    foreach ($precaches as $key => $precache):?>
    <li><input type="checkbox" name="mcw_precaches[]" class="precaches" value="<?php echo $precache;?>"/> <?php echo $precache;?></li>
    <?php endforeach;?>
</ul>
<?php submit_button('Update Precaches');?>
</form>