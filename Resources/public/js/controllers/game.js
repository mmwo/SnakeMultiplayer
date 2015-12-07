angular.module("snake")
.controller("game", ["$scope", "$wamp","$q", "$filter", function($scope, $wamp,$q, $filter){
    $scope.data = {players:{}, bugs:{}};
    
    $scope.fields = [];
    $scope.gameCreation = false;

    $scope.snakeDirection = "";
    $scope.playersListView = 'online';
    $scope.disableGameOverInfo = function(){
        $scope.GameOverInfo = false;
    }
    $scope.setPlayersListView = function(view){
        $scope.playersListView = view;
    }
    $scope.anonymousAppeared = function(){
        $scope.anonymousPlayers = true;
        return false;
    }
    $scope.playersOnline = function(){
        return $filter("unique")($filter("online")($scope.data.players)).length;
    }
    $scope.createGame = function(){
        function success(success){
            $scope.addPlayer();
            
        };
        function fail(fail){};
        
        if(!$scope.gameCreation){
            $wamp.call("games.snake.startgame", [{}]).then(success, fail);
        }
    }
    $scope.cancelCreation = function(){
        $scope.gameCreation = false;
    }
    $scope.isGame = function(){
        return ($filter("playerList")($scope.data.players,'ingame').length > 0)? true : false;
    }

    $scope.addPlayer = function(){
        $wamp.call("games.snake.newplayer",[{sessionId: $wamp.connection.session.id}]).then(
                function(snake){
                    $scope.data.ingame = snake.ingame ;
                    $scope.snakeDirection = snake.direction;
                    var headsOfOthers = angular.element(document.getElementsByClassName("myHead")[0]);
                        headsOfOthers.removeClass("myHead");
                    var myHead = document.getElementsByClassName(snake.className+ " snakeHead");
                        myHead = angular.element(myHead[0]);
                        myHead.addClass("myHead");
                },
                function(error){
                    console.log("error",error);
                });
    }

    $scope.$watch("gameCreation", function(change){
        if(change=== true){
            $scope.setPlayersListView("nowPlaying");
            $scope.disableGameOverInfo();
            
            angular.forEach($scope.data.players, function(player,key){
                $scope.data.players[key].nowPlaying = false;
            })
        }
    })
    $scope.$watch("data.players", function(players){   
//        console.log(players);
            var snakes = [];
            var bugs = [];
            
            players = $filter("playerList")(players,'ingame');
            if(players.length == 0){
                $scope.lockNavigation();
                $scope.fields = [];
                $scope.data.bugs = [];
            }else{
                angular.forEach(players, function(player){
                var i = 0;
                    angular.forEach(player.body, function(elem){
                        className = (i==0)? player.className + " snakeHead" : player.className;
                        i++;
                        elemWithClassName = elem;
                        elemWithClassName.className = className;
                        snakes.push(elemWithClassName);
                    })
                
                });
                $scope.fields = snakes;
                for(var bug in $scope.data.bugs){
                $scope.fields.push($scope.data.bugs[bug]);
            }
            
            
        }
            
    }, true);

    $scope.$on("$wamp.open", function(dontKnowWhat, response){
        
        function updatePlayers(id, values){
            if(typeof $scope.data.players[id] ==="undefined"){
                $scope.data.players[id] = {};
            }
            angular.forEach(values, function(value, key){
                $scope.data.players[id][key] = value;
                if($scope.data.players[id].ingame === true){
                    $scope.data.players[id].nowPlaying = true;
                }
            });

        }    
        function onevent(results){
             if(!angular.isUndefined(results[0].data)){    
                updatePlayers(results[0].id, results[0].data); 
            }
            if(!angular.isUndefined(results[0].bugs)){
                $scope.data.bugs = results[0].bugs;
            }
            if(!angular.isUndefined(results[0].gameCreation)){
                $scope.gameCreation = results[0].gameCreation;
            }
        }

        $wamp.subscribe("games.snake.game", onevent).then(
                function(result){
                    $data =  [{sessionId: $wamp.connection.session.id,
                               name: response.details.authid,
                               time: new Date().toISOString()
                        }];
                    
                   $q(function(){
                       $wamp.call("games.snake.activity", $data)
                       setInterval(function(){
                        $data[0].time = new Date().toISOString();
                           $wamp.call("games.snake.activity", $data)
                                .then(function(){},function(error){
                                    console.log(error);
                                });
                       },5000)
                   })               
                }, function(){
                    console.log("not subscribed");
                });
    });
    
}]);