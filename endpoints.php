<?php
include('config.php');

function parseWopiRequest($uri) {
   global $MAIL_SERVER_OFFICE_DIRECTORY;

   $path = parse_url($uri, PHP_URL_PATH);
   $x = explode($MAIL_SERVER_OFFICE_DIRECTORY . "/wopi/files/", $path)[1];
   $x = explode("/contents", $x)[0];
   $x = base64_decode($x);

   if (str_contains($path, '/contents')) {
      switch ($_SERVER['REQUEST_METHOD']) {
         case  'GET': wopiGetFile($x); break;
         case 'POST': wopiPutFile($x); break;
      }
   } else {
      wopiCheckFileInfo($x);
   }
}

function wopiCheckFileInfo($documentId) {
   $headers = get_headers($documentId, true);

   preg_match('/(?<=\'\'|=")[^"\n]+/', $headers['Content-Disposition'], $matches);

   $response = [
      'BaseFileName' => urldecode($matches[0]),
      'UserId' => 'webmail',
      'UserCanWrite' => false,
      'UserCannotWriteRelative' => true,
      'HideSaveOption' => true,
      'DisableCopy' => true	    
   ];

   echo json_encode($response);
}

function wopiGetFile($documentId) {
   echo file_get_contents($documentId);
}

parseWopiRequest($_SERVER['REQUEST_URI']);
?>
