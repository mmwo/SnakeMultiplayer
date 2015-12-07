angular.module("snake")
.factory("keyAction",function(){
     return {
         action: function(keyCode){
             switch(keyCode){
                case 37: return "l";
                case 38: return "u";
                case 39: return "r";
                case 40: return "d";
             }
         }
     }       
})

