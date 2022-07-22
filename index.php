<?php
include('config.php');

$errorMsg = [
    101 => 'GET Request not found',
    201 => 'Collabora Online server address is not valid',
    202 => 'Collabora Online server address scheme does not match the current page url scheme',
    203 => 'No able to retrieve the discovery.xml file from the Collabora Online server with the submitted address.',
    102 => 'The retrieved discovery.xml file is not a valid XML file',
    103 => 'The requested mime type is not handled',
    204 => 'Warning! You have to specify the scheme protocol too (http|https) for the server address.'
];

function getDiscovery($server) {
    $discoveryUrl = $server.'/hosting/discovery';
    $res = file_get_contents($discoveryUrl);
    return $res;
}

function getWopiSrcUrl($discovery_parsed, $mimetype) {
    if ($discovery_parsed === null || $discovery_parsed == false) {
        return null;
    }
    $result = $discovery_parsed->xpath(sprintf('/wopi-discovery/net-zone/app[@name=\'%s\']/action', $mimetype));
    if ($result && count($result) > 0) {
        return $result[0]['urlsrc'];
    }
    return null;
}

function strStartsWith($s, $ss) {
    $res = strrpos($s, $ss);
    return !is_bool($res) && $res == 0;
}

$_WOPI_SRC = '';

function main() {
    global $_WOPI_SRC;
    global $CODE_SERVER;

    $wopiClientServer = $CODE_SERVER;
    if (!$wopiClientServer) {
        return 201;
    }
    $wopiClientServer = trim($wopiClientServer);

    if (!strStartsWith($wopiClientServer, 'http')) {
        return 204;
    }

    $discovery = getDiscovery($wopiClientServer);
    if (!$discovery) {
        return 203;
    }

    $loadEntities = libxml_disable_entity_loader(true);
    $discovery_parsed = simplexml_load_string($discovery);
    libxml_disable_entity_loader($loadEntities);
    if (!$discovery_parsed) {
        return 102;
    }

    $_WOPI_SRC = getWopiSrcUrl($discovery_parsed, 'text/plain');
    if (!$_WOPI_SRC) {
        return 103;
    }

    return 0;
}

$errorCode = main();
?>


<!-- Initiator form -->
<html lang="en">
<body>
<div style="display: none">
    <form action="" enctype="multipart/form-data" method="post" target="_self" id="collabora-submit-form">
	<input name="access_token" value="sample" type="hidden" />
	<input name="ui_defaults" value="UIMode=tabbed;TextRuler=false;PresentationStatusbar=false;SpreadsheetSidebar=false;" type="hidden" />
	<input name="css_variables" value="--co-color-main-text=#000;--co-body-bg=#FFF;--co-txt-accent=#2e1a47;" type="hidden"/>
        <input type="submit" value="" />
    </form>
</div>
<div>
    <p> Something went wrong :-( </p>
    <p> <?php
        if ($errorCode > 200)
            echo $errorMsg[$errorCode];
        ?>
    </p>
</div>

<!-- Submitter script -->
<script type="text/ecmascript">
    function loadDocument() {
	var wopiSrc = window.location.origin + "<?php echo $MAIL_SERVER_OFFICE_DIRECTORY; ?>" +'/wopi/files/' + "<?php echo base64_encode(urldecode($_GET['url'])); ?>";

        var wopiClientUrl = "<?php echo $_WOPI_SRC; ?>";
        if (!wopiClientUrl) {
            console.log('error: wopi client url not found');
            return;
        }

        var wopiUrl = wopiClientUrl + 'WOPISrc=' + wopiSrc;

        var formElem = document.getElementById("collabora-submit-form");
        if (!formElem) {
            console.log('error: submit form not found');
            return;
        }
	formElem.action = wopiUrl;
        formElem.submit();
    }

    loadDocument();
</script>

<!-- Actual iFrame for Collabora CODE -->
<iframe id="collabora-online-viewer" name="collabora-online-viewer" style="width:95%;height:80%;position:absolute;"></iframe>
</body>
</html>
