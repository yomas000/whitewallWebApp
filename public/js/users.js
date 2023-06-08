function getUser(e) {
    var id = e.id;
    var tableRow = $(e);

    if (lastElement != "") {
        lastElement.css("background-color", "white");
    }

    tableRow.css("background-color", "#c8cbcf");
    lastElement = tableRow;

    $("#userPrompt").hide();
    $("#userForm").show();

    $("#permissionsForm").prop("user-id", id);

    $.get("user", {
        id: id
    }, function (data, status) {
        if (status == "success") {
            user = JSON.parse(data).user
            permissions = JSON.parse(data).permissions

            $("#name").val(user.name);
            $("#email").val(user.email);
            $("#admin").prop("checked", Boolean(Number(permissions.admin)));
            $("#active").prop("checked", Boolean(Number(user.status)));

            if (Boolean(Number(permissions.admin))){
                $("[name$='[]']").prop("disabled", true);
            }

            //set permissons
            let viewamount = 0;
            let addamount = 0;
            let editamount = 0;
            let removeamount = 0;
            for (const key in permissions) {
                $(`[name = permissions\\[${key}\\]\\[view\\]\\[\\]]`).prop("checked", Boolean(Number(permissions[key].view)))
                $(`[name = permissions\\[${key}\\]\\[add\\]\\[\\]]`).prop("checked", Boolean(Number(permissions[key].add)))
                $(`[name = permissions\\[${key}\\]\\[edit\\]\\[\\]]`).prop("checked", Boolean(Number(permissions[key].edit)))
                $(`[name = permissions\\[${key}\\]\\[remove\\]\\[\\]]`).prop("checked", Boolean(Number(permissions[key].remove)))

                if (Boolean(Number(permissions[key].view))){
                    viewamount++
                }
                if (Boolean(Number(permissions[key].add))) {
                    addamount++
                }
                if (Boolean(Number(permissions[key].edit))) {
                    editamount++
                }
                if (Boolean(Number(permissions[key].remove))) {
                    removeamount++
                }
            }
            if (viewamount == 8){
                $("[name='permissions[all][view][]']").prop("checked", true);
            }
            if (addamount == 8) {
                $("[name='permissions[all][add][]']").prop("checked", true);
            }
            if (editamount == 8) {
                $("[name='permissions[all][edit][]']").prop("checked", true);
            }
            if (removeamount == 8) {
                $("[name='permissions[all][remove][]']").prop("checked", true);
            }

            console.log(viewamount);
        } else {
            alert(JSON.parse(data).msg)
        }
    })
}

$("#admin").on("click", function(){
    $("[name^='permissions[']").prop("checked", $("#admin").prop("checked"));

    if ($("#admin").prop("checked")){
        $("[name^='permissions[']").prop("disabled", true);
    }else{
        $("[name^='permissions[']").prop("disabled", false);
    }
})

$("#permissionsForm").submit(function(e){
    e.preventDefault();

    formData = new FormData(document.getElementById("permissionsForm"));

    formData.append("userId", $("#permissionsForm").prop("user-id"));

    for (const value of formData.values()) {
        console.log(value);
    }

    $.ajax({
        url: "/brand/users/update",
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function (data) {
            response = JSON.parse(data);
            console.log(response);
            if (response.success) {
                $(".alert-success").show();
            } else {
                $(".alert-danger").html(response.message);
                $(".alert-danger").show();
            }
        }
    });
})

$("[name='permissions[all][view][]']").on("click", function(e){
    $("[name$='[view][]']").prop("checked", $(e.target).prop("checked"))
})
$("[name='permissions[all][add][]']").on("click", function (e) {
    $("[name$='[add][]']").prop("checked", $(e.target).prop("checked"))
})
$("[name='permissions[all][edit][]']").on("click", function (e) {
    $("[name$='[edit][]']").prop("checked", $(e.target).prop("checked"))
})
$("[name='permissions[all][remove][]']").on("click", function (e) {
    $("[name$='[remove][]']").prop("checked", $(e.target).prop("checked"))
})