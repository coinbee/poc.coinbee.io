<?php

require_once('retrieveaddress.php');

//step 1, user arrives at page
//step 11, our front end said the user paid. confirm this with a serverside call
$t = retrieveAddressForToken();

//step 3, data initialized from coinbee response
$can_see = isset($t['results']['paid']) ? $t['results']['paid'] : false;
$address = isset($t['results']['address']) ? $t['results']['address'] : '';
$required_btc = isset($t['results']['required_btc']) ? $t['results']['required_btc'] : '';

?>
<!doctype html>
<html ng-app="app" ng-controller="poc">
    <head>
        <link rel="stylesheet" href="assets/css/bootstrap.min.css">

        <script type="text/javascript">
            var address = "<?php echo $address; ?>"
            var required_btc = "<?php echo $required_btc; ?>"
        </script>

        <script src="assets/js/qrcode_lib.js"></script>
        <script src="assets/js/jquery.min.js"></script>
        <script src="assets/js/angular.min.js"></script>
        <script src="assets/js/app.js"></script>
        <script src="assets/js/qrcode.js"></script>
    </head>
    <body ng-cloak>
    <?php
    //step 12, if coinbee confirmed the given user identifier paid for the content, show it. That's it.
    if ($can_see)
    {
        ?>
        <div>You've paid for the premium content!</div>
        <?php
    }
    else
    {
        ?>
        <div class="container">
            <div>
                <h2>Example Paywalled Content Teaser Heading</h2>
                <p>The locked content can be anything from text, a video, an image or an entire page.</p>
                <p>You can set your price for each individual piece of content on the fly as well via Coinbee's API.</p>
                <p>Once we tell you via an API request that the user's paid, it's up to you to decide how long the user can access the current piece of content, or to even allow them to consume other content (like a day pass)</p>
                <p>Utilizing a user identifier on address generation allows accidental page reloads to not lose a user's address or payments. Also useful for reporting on your site</p>
                <p>Click the button below and send bitcoin to the generated unique address to access the locked content on this page.</p>
            </div>

            <div class="text-center">
                <hr>
                <div ng-show="address == ''">
                    <!-- step 4, the user requests a bitcoin address by doing a manual button click -->
                    <button class="btn btn-success" ng-click="showAddress()">Unlock with bitcoin</button>
                    <div ng-show="showAddressLoader" style="display: inline-block">
                        <img src="assets/img/ajax-loader-small.gif">
                        <span>Generating address...</span>
                    </div>
                </div>
                <div ng-show="address != ''">
                    <!-- step 8, show the data to the user -->
                    <h3>Please send "{{required_btc}}" bitcoins to address<br>"{{address}}" in order to unlock this content<br>
                    <small>Once payment is received you will be redirected automatically, no need to refresh the page</small></h3>
                    <qrcode size="200" data="{{address}}"></qrcode>
                    <div ng-show="showAddressCheckerLoader">
                        <img src="assets/img/ajax-loader-small.gif">
                        <span>Actively waiting for {{confirmations}} confirmations...</span>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    ?>

    <div style="bottom: 0px; position: absolute;">
        <p>Go <a href="https://portal.coinbee.io/#/docs" target="_blank">here</a> to view the Coinbee API</p>
        <p>This site uses Google Analytics for demographics tracking</p>
    </div>
    <script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

        ga('create', 'UA-61662824-2', 'auto');
        ga('send', 'pageview');

    </script>
    </body>
</html>
