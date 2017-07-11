<?php
/* * Copyright 2011 Google Inc.
*
* Licensed under the Apache License, Version 2.0 (the "License");
* you may not use this file except in compliance with the License.
* You may obtain a copy of the License at
*
* http://www.apache.org/licenses/LICENSE-2.0
*
* Unless required by applicable law or agreed to in writing, software
* distributed under the License is distributed on an "AS IS" BASIS,
* WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
* See the License for the specific language governing permissions and
* limitations under the License.
*/
//replace --- with your own api and secret keys obtained while registgering for your new app in Google API Console
require_once 'google-api-php-client/src/Google_Client.php';
require_once 'google-api-php-client/src/contrib/Google_PlusService.php';

session_start();

$id = $_POST['id'];

$client = new Google_Client();
$client->setApplicationName("Google+ PHP Starter Application");
// oauth2_client_id, oauth2_client_secret, and to register your oauth2_redirect_uri.
$client->setClientId('715402557441-tr3l1g35v5qk7t1a3q7uoasddq65hf8i.apps.googleusercontent.com');
$client->setClientSecret('lGiqnuUIE4y5VLMq4lRiA1cm');
$client->setRedirectUri('http://webservice86.esy.es');
$client->setDeveloperKey('AIzaSyBs5AWuR_qqVLo3jerdNBtJ-YWoze9RYkE');
$plus = new Google_PlusService($client);

if (isset($_REQUEST['logout'])) {
unset($_SESSION['access_token']);
}

if (isset($_GET['code'])) {
$client->authenticate();
$_SESSION['access_token'] = $client->getAccessToken();
header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
}

if (isset($_SESSION['access_token'])) {
$client->setAccessToken($_SESSION['access_token']);
}

if ($client->getAccessToken()) {
$me = $plus->people->get($id);

$optParams = array('maxResults' => 100);
$activities = $plus->activities->listActivities($id, 'public', $optParams);

// The access token may have been updated lazily.
$_SESSION['access_token'] = $client->getAccessToken();
} else {
$authUrl = $client->createAuthUrl();
}
?>

<!doctype html>
<html>
<head><link rel='stylesheet' href='style.css' /></head>
<body>
<header><h1>Google+ Sample App</h1></header>
<div class="box">

<?php if(isset($me) && isset($activities)): ?>
<div class="me">
<p><a rel="me" href="<?php echo $me['url'] ?>"><?php print $me['displayName'] ?></a></p>
<p><?php print $me['tagline'] ?></p> 
<p><?php print $me['aboutMe'] ?></p> 
<div><img src="<?php echo $me['image']['url']; ?>?sz=82" /></div>
</div>
<div class="activities">Your Activities:
<?php foreach($activities['items'] as $activity): ?>
<div class="activity">
<p><a href="<?php print $activity['url'] ?>"><?php print $activity['title'] ?></a></p>
</div>
<?php endforeach ?>
</div>
<?php endif ?>
<?php
if(isset($authUrl)) {
print "<a class='login' href='$authUrl'>Connect Me!</a>";
} else {
//print "<a class='logout' href='?logout'>Logout</a>";
}
?>
</div>
</body>
</html>
