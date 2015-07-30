$(document).ready(function(){
    $(".zoom a.thumb").append("<span></span>");
    $(".zoom a.thumb").hover(function(){
        $(this).children("span").fadeIn(600);
    },function(){
        $(this).children("span").fadeOut(200);
    });
});