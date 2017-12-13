<?
function etag_start() {
    global $etag_depth;

    if($etag_depth == 0) ob_start();
    $etag_depth++;
  }

function etag_end() {
global $etag_depth;
$etag_depth--;
if($etag_depth > 0) return;

$content = ob_get_clean();
$etag = hash('sha256', $content);
$request = $_SERVER['HTTP_IF_NONE_MATCH'];
if($etag == $request) {
    http_response_code(304);
    return;
}

header('Etag: ' . $etag);
echo $content;
}