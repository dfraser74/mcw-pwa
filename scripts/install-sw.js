(async function() {
    
    if(!('serviceWorker' in navigator)) {
      return;
    }
    console.log('Plugin URL');
    console.log(_wordpressConfig.pluginUrl);
    navigator.serviceWorker.register(`${_wordpressConfig.pluginUrl}scripts/sw.php`, {scope: '/'});
    
    })();