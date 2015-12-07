angular.module("snake")
.filter("playerList", function(){
    return function(players,condition){
        var playerList = [];
        angular.forEach(players, function(player){
            if(!angular.isUndefined(player[condition]) && player[condition] === true){
                playerList.push(player)
            }
        });
        return playerList;
    }
})
.filter("unique", function(){
    return function(players){
        var unique = {};
        var results = [];
        angular.forEach(players, function(player){
            if(angular.isUndefined(unique[player.name])){
                unique[player.name] = true;
                results.push(player);
            }
        });
        return results;
    }
})
.filter("online", function(){
    return function(players){
        var playersOnline = [];
        now = new Date(new Date().getTime() - 10000);
        angular.forEach(players, function(player){
            if(!angular.isUndefined(player.time)&& new Date(player.time) > now){
                playersOnline.push(player);
            }
        });
        return playersOnline;
    }
})

