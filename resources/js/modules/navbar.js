(function ($) {
    // Make main navigation top level links clickable
    $(".nav-link.dropdown-toggle").on("click", function (event) {
        event.preventDefault();
        event.stopImmediatePropagation();

        var href = $(this).attr("href");
        if (href) {
            window.location = href;
        }
    });
})(window.jQuery || window.$);
