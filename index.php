<html> <body>
<a href="http://ec2-50-19-20-65.compute-1.amazonaws.com/create_account.html">Create account</a>
<a href="http://ec2-50-19-20-65.compute-1.amazonaws.com/login.php">Login</a>
<a href

<a href="https://foursquare.com/oauth2/authenticate?client_id=3B53D2V4SEVOHI1R5LNL1H50N4400SQO2JKJSO5MMSP4FLIF&response_type=token&redirect_uri=http://ec2-50-19-20-65.compute-1.amazonaws.com/Oauth.php">Oath</a>
<a href="https://api.foursquare.com/v2/users/self/checkins?oauth_token=EYVRQAWL5OJAOVVHFFAKYJDSNJVRPOWF0XFGHG3MBWBWEYD3">Checkins</a>

<?php 
 
foreach($decoded as $user){

    echo "<a href=\"http://ec2-50-19-20-65.compute-1.amazonaws.com/profile.php?\"" . $user. ">" . $user . "</a><br>"
}
?>
</body> </html>
