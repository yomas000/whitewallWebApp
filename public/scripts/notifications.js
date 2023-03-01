function notifications_start() {
    $("#force-switch").on("click", function () {
        var checked = $("#force-switch")[0].checked;

        if (checked) {
            $("#force-option").hide();
            $("#force-select-div").show();
        } else {
            $("#force-option").show();
            $("#force-select-div").hide();
        }
    })

    $("#selections").on("click", function () {
        var option = $("#selections").val();
        if (option == "Wallpaper") {
            $("#wall-select").show();
        } else {
            $("#wall-select").hide();
        }

        if (option == "Link") {
            $("#link-input").show();
        } else {
            $("#link-input").hide();
        }
    })
}