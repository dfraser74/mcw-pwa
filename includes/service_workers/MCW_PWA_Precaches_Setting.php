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
        list.innerHTML = `<input type="checkbox" name="mcw_precaches[]" class="precaches" value="${newPrecache.value}" checked/> ${newPrecache.value} <input type="hidden" name="mcw_assets[]" class="assets" value="${newPrecache.value}"/> <a href="javascript:void(0);" onclick="removeAsset(this)">Remove</a>`.trim();
        precaches.append(list);
        newPrecache.value='';
    }

    function removeAsset(el){
        let list=el.parentElement.parentElement;
        list.removeChild(el.parentElement);
    }

</script>
<div id="add-precache">
    <input type="text" id="new-precache" size="50"/> <button class="button-primary" onclick="addPrecache()">Add Precache</button>
</div>

<div id="preaches-container">
<form method="POST">
<?php wp_nonce_field('mcw_precaches_update');?>
<h3>Preaches Files</h3>
<p class="description">Check files below that you want to enable as precaches assets. Unchecked files will not precaches.</p>
<ul id="precaches-list">
    <li><input type="checkbox" value="select_all" onclick="selectAllCaches(this)"/> <strong style="font-size:1.2em;">Select All Preaches</strong></li>
<?php
    $precaches = MCW_PWA_Service_Worker::instance()->getPrecaches();
    foreach ($precaches as $key => $precache):?>
        <li>
            <input type="checkbox" name="mcw_precaches[]" class="precaches" value="<?php echo $precache;?>" checked/> <?php echo $precache;?> <a href="javascript:void(0);" onclick="removeAsset(this)">Remove</a>
            <input type="hidden" name="mcw_assets[]" class="assets" value="<?php echo $precache;?>"/>
        </li>
    <?php endforeach;?>
<?php 
    $assets=MCW_PWA_Service_Worker::instance()->getAssets();
    if(is_array($assets)):
    foreach ($assets as $key => $precache):?>
        <li>
            <input type="checkbox" name="mcw_precaches[]" class="precaches" value="<?php echo $precache;?>"/> <?php echo $precache;?> <a href="javascript:void(0);" onclick="removeAsset(this)">Remove</a>
            <input type="hidden" name="mcw_assets[]" class="assets" value="<?php echo $precache;?>"/>
        </li>
    <?php endforeach;?>
<?php endif;?>
</ul>
<?php submit_button('Update Precaches');?>
</form>
</div>

