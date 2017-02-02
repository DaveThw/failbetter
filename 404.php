<?php
include_once('./edit/main_generator.php');

if ($_SERVER['REDIRECT_STATUS'] == 404) {
 $dir = explode("/", trim(dirname(collapse_filename($_SERVER['REDIRECT_SCRIPT_URL']."file")),"/"));

 // $home_dir is a variable that gives us a relative referance to the home level directory
 // ie. "." for pages in the home directory
 //     ".." for pages one level down
 //     "../.." for pages two levels down, etc.

 // $this_dir is a variable that gives us a referance to this directory, from the home directory
 // ie. "" for pages in the home directory
 //     "company/" or "press/" for pages one level down
 //     "press/stasis/" for pages two levels down, etc.
 // note: *without* 'edit/' in it!..

 if (count($dir) == 0) {
  $home_dir = ".";
  $this_dir = "";
 } else {
  $home_dir = str_repeat("../", count($dir)-1) . "..";
  $this_dir = implode ("/", $dir) . "/";
 }
}

$page['Title'] = "File Not Found - Fail Better Productions";
//$page['Image'] = "./collage-rough2.jpg";

$page['Stylesheets'][] = array('href'=>$home_dir."/global.css");
$page['Stylesheets'][] = array('href'=>$home_dir."/menu.css");
$page['Stylesheets'][] = array('href'=>$home_dir."/404.css");
$page['IE6Stylesheets'][] = array('href'=>$home_dir."/global-ie6.css");
$page['IEStylesheets'][] = array('href'=>$home_dir."/global-ie.css");

$request['title_blank'] = true;
$request['swapcontact'] = false;
$request['stripindex'] = true;
$request['validateself'] = true;
$request['output'] = "error";

$pure_output = true;

header("HTTP/1.1 404 Not Found");
 
generate_html_head($page);
generate_html_content($page);
generate_html_tail($page);

function generate_html_content($page) {
  global $dir, $home_dir;
  write('    <div class="sub-content" style="overflow:auto">');
  write("     <h1>File not found!</h1>");
//  write("     <p>We're sorry, but the page you requested (http://" . $_SERVER['SERVER_NAME'] . $_SERVER['REDIRECT_URL'] . ") cannot be found. If you typed the address yourself, please check that you have typed it correctly.</p>");
  write("     <p>We're sorry, but the page you requested (" . $_SERVER['REDIRECT_SCRIPT_URI'] . ") cannot be found. If you typed the address yourself, please check that you have typed it correctly.</p>");
  write("     <p>If you have reached this page from a link on our website, please <a href=\"$home_dir/contact.php\">contact us</a> to inform us of the problem</p>");
  write("     <p>Please either use the 'Back' button in your browser to return to the page you were viewing last, or use the menu on the left to navigate around our site.</p>");
//<style type="text/css">
//  #goog-wm { }
//  #goog-wm h3.closest-match { }
//  #goog-wm h3.closest-match a { }
//  #goog-wm h3.other-things { }
//  #goog-wm ul li { }
//  #goog-wm li.search-goog { display: block; }
//</style>
  write('     <script type="text/javascript">');
  write("      var GOOG_FIXURL_LANG = 'en_GB';");
  write("      var GOOG_FIXURL_SITE = 'http://www.failbetter.co.uk/';");
  write("     </script>");
  write('     <script type="text/javascript" src="http://linkhelp.clients.google.com/tbproxy/lh/wm/fixurl.js"></script>');

// From the matchbet website:

// File Not Found
// --------------
// We're sorry, but we were unable to locate the file (page) you requested. If you have typed the url (address) yourself, please check that you have typed it correctly.
//
// If you have reached this page from a link on our website, please Contact Us to inform us of the problem. Thank you.
//
// Navigating from here
// --------------------
// You have a number of options from this page:
// 
// - Use the "Back" button in your browser to return to the page you were viewing last 
// - Use the menu above to navigate around our site 
// - Visit our Site Map to find the page you require 
// - Use the search bar below to scan our site for the page you are looking for


  // interesting variables to log:
  // $_SERVER['HTTP_REFERER']
  // $_SERVER['HTTP_USER_AGENT']
  // $_SERVER['HTTP_HOST']
  // $_SERVER['SERVER_NAME']
  // $_SERVER['SERVER_PORT']
  // $_SERVER['REMOTE_ADDR']
  // $_SERVER['REDIRECT_URL']
  // $_SERVER['REQUEST_URI']
  // $_SERVER['REDIRECT_QUERY_STRING']
  // $_SERVER['REDIRECT_STATUS']
  // $_SERVER['REDIRECT_REQUEST_METHOD']
  // $_SERVER['REDIRECT_SCRIPT_URI']
  // $_SERVER['REDIRECT_ERROR_NOTES']
  //  This last one seems to be used for error 500 (Server Error) to pass on the error text

  // Send an email to Dave...
  $body = "File not found: " . $_SERVER['REDIRECT_SCRIPT_URI'] . "\n";
  $body .= "Referer: " . ($_SERVER['HTTP_REFERER'] ? $_SERVER['HTTP_REFERER'] : "<none>") . "\n";
  $body .= "User Agent: " . $_SERVER['HTTP_USER_AGENT'] . "\n";
  $body .= "Remote Address: " . $_SERVER['REMOTE_ADDR'] . "\n";
  $body .= "\n\n\$_SERVER = " . trim(print_r($_SERVER, true)) . "\n";
  $body = wordwrap ($body, 70);
  @mail("failbetter-web@dave.thwaites.org.uk", "[failbetter.co.uk] File Not Found at FailBetter.co.uk", $body, "From: Fail Better Website <info@failbetter.co.uk>\r\nReply-To: \"Change this address!..\" <failbetter-web@dave.thwaites.org.uk>\r\n");

//  write("     <pre>");
//  echo '$_SERVER = ';
//  print_r($_SERVER);
//  echo '$dir = ';
//  print_r($dir);
//  write("     </pre>");
  write('    </div>');
}
?>