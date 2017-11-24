$(document).mouseleave(function(e){
    if (e.clientY < 0) {
        $(".exitblock").fadeIn("fast");
    }
});
$(document).click(function(e) {
    if (($(".exitblock").is(':visible')) && (!$(e.target).closest(".exitblock .modaltext").length)) {
        $(".exitblock").remove();
    }
});