var app = angular.module('app', ['controllers']);

var controllers = angular.module('controllers', []);

controllers.controller('poc', ['$scope', '$http', '$interval', '$window',
    function($scope, $http, $interval, $window) {
        $scope.showAddressLoader = false;
        $scope.confirmations = 0;
        $scope.showAddressCheckerLoader = false;
        $scope.address = address;
        $scope.required_btc = required_btc;

        $scope.$watch('address', function(newVal, oldVal) {
            $scope.showAddressCheckerLoader = true;
            if ($scope.address != '') {
                //step 9, our $watch is triggered because the FE address is now set. Periodically ask coinbee about payment status (Front end)
                $interval(function () {
                    //we can use this to allow cross domain requests, but CORS is utilized as well if you wish to use it
                    //some corporate firewalls (watchguard) don't allow CORS headers through so this is preferred
                    //it won't really break anything it just means the user will need to manually refresh the page
                    $http.jsonp('https://api.coinbee.io/retrieve/address/' + $scope.address + '?callback=JSON_CALLBACK')
                        .success(function (data) {
                            if (data.status == 'ok') {
                                if (data.results.paid == true) {
                                    //step 10, coinbee confirms the user paid. Reload the page to ask coinbee via server side if this is true
                                    //remember, always confirm front end responses
                                    $window.location.reload();
                                }
                            }
                        });
                }, 20000);
            }
        });

        $scope.showAddress = function() {
            $scope.showAddressLoader = true;

            //step 5, our front end makes a back end server call (still on content provider site)
            $http.get('retrieveaddress.php?proc=1')
                .success(function(data) {
                    $scope.showAddressLoader = false;

                    if (data.status == 'ok') {
                        //step 7, we present the address and payment info to the user
                        $scope.address = data.results.address;
                        $scope.required_btc = data.results.required_btc;
                    }
                }
            );
        };
    }
]);