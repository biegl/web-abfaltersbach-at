(function($) {
    function setCookie(cname, cvalue, exdays) {
        var d = new Date();
        d.setTime(d.getTime() + exdays * 24 * 60 * 60 * 1000);
        var expires = "expires=" + d.toUTCString();
        document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
    }

    var jDisclaimer = $("#tcn_notice");

    // Add switch listener
    jDisclaimer.find("input:checkbox").change(function() {
        if (this.checked) {
            setCookie("ga-disabled", "", -365);
        } else {
            setCookie("ga-disabled", "true", 365);
        }
    });

    jDisclaimer.find(".close_notice").click(function() {
        setCookie("consent", "true", 365);
        jDisclaimer.fadeOut("slow", function() {
            $(this).remove();
        });
    });
})(window.jQuery || window.$);
