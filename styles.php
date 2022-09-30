<?php
  // CSS files to compress
$files = array(
  '/css/normalize-3.0.2.min.css',
  '/blueimp-gallery-2.27.0/css/blueimp-gallery.min.css',
  '/bootstrap/css/bootstrap.css',
  '/css/base.css',
);

  // Headers
header('Content-type: text/css');

  // Minify
function minify($buffer)
{
    // remove comments
  $buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
    // remove tabs, spaces, newlines, etc.
  $buffer = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $buffer);
  $buffer = str_replace(array(', ',' {','} ','{ ',' }',': ','; '), array(',','{','}','{','}',':',';'), $buffer);
  return $buffer;
}

  // Include CSS files
$css = '';
foreach ($files as $file) {
  if (is_readable($file)) {
    $css .= minify(file_get_contents($file));
  }
}
ob_start("ob_gzhandler");
echo($css);
ob_end_flush();
