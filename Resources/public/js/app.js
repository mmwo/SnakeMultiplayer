angular.module("snake", ["vxWamp", "ui.bootstrap"])
.config(function($interpolateProvider, $wampProvider){
    $interpolateProvider.startSymbol("[[");
    $interpolateProvider.endSymbol("]]");
    $wampProvider.init({
        url: "ws://"+CONST("websiteUrl")+":8081/games",
        realm: "realm1",
        authmethods: ["wampcra"],
        authid: CONST("user")
    });
})
.run(function($wamp){
    $wamp.open()
})



