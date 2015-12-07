angular.module("snake")
.directive("snakeNavigation", ["keyAction","$q","$wamp", function(keyAction, $q, $wamp){
    return{
        restrict: "A",
        replace: true,
        controller: function($scope){
            $scope.canNavigate = false;
            $scope.data.ingame = false;
            
            $scope.unlockNavigation = function(){
                $scope.canNavigate = true;
            }
            $scope.lockNavigation = function(){
                $scope.canNavigate = false;
            }

            $scope.$watch("data.ingame", function(newState, oldState){
                if(oldState === true && newState === false){
                    $scope.GameOverInfo = true;
                }
            });
        },
        link: function(scope, element, attr){
  
            function sendAction(){
                return $q(function(resolve, reject){
                 var movement = setInterval(function(){
                            if(!scope.snakeDirection=="" && scope.data.ingame && scope.canNavigate){
                                $wamp.call("games.snake.move", [{
                                        direction:scope.snakeDirection,
                                        sessionId:$wamp.connection.session.id
                                }])
                                .then(
                                    function(res){
                                        scope.data.ingame = res.ingame ;
                                        if(res.ingame === false) {
                                            scope.lockNavigation();
                                        }
                                    },
                                    function(err){
                                        console.log("failed to call rpc", err);
                                    })
                            }
                     }, 300);
                 });
             }
             if(CONST("user") !== "anonymous"){
                document.addEventListener("keydown", function(e){
                    e.preventDefault();

                    if(keyAction.action(e.keyCode)){
                        scope.snakeDirection = keyAction.action(e.keyCode);   
                    }else if(e.keyCode = 13 || e.which == 13){
                        scope.disableGameOverInfo();
                    }
                });

                var promise = sendAction();
             }
        }
            
    }
}])