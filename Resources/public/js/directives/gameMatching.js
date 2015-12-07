angular.module("snake")
.directive("gameMatching", function($timeout){
   return { 
            replace: true,
            template: '<div class="panel panel-info game-matching">'+
                '<h4>Game matching</h4>'+
                '<div class="matched">'+
                    '<div class="well player-matched" '+
                         'ng-repeat="player in data.players | playerList:\'ingame\' track by $index"'+
                         'tooltip-class="player-tooltip" uib-tooltip="[[player.name]]"><span>[[playerNumbers($index)]]</span></div>'+
                '</div>'+
                '<div id="progress">'+
                   '<div class="custom-progress-bar"></div>'+
                '</div>'+

                '<button class="btn btn-primary btn-sm pull-right"'+ 
                        'ng-click="addPlayer()" ng-disabled="data.ingame">'+
                    'Accept'+
                '</button>'+
                
            '</div>',
            controller: function($scope){
              $scope.playerNumbers = function(index){
                numbers = ["S","N","A","K","E"];
                 
                return !angular.isUndefined(numbers[index])? numbers[index]:index;
              }
            },
            link: function(scope, elem, attr){
                var progressState = 0;              
                var progress =  angular.element(elem[0].querySelector(".custom-progress-bar"));                                   
                var progressBar = setInterval(function(){
                        if(progressState < 100 && scope.gameCreation){
                            progressState +=20;
                            progress.css({width:progressState+"%"});
                        }else{                            
                            scope.cancelCreation();
                            setTimeout(function(){
                                scope.unlockNavigation();
                                clearInterval(progressBar);  
                            },2000);
                        }
                    }, 800);  
            }
   };
})

