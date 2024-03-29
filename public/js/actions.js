function toggleAll(){
    var boxes = document.getElementsByClassName("checkbox-lg");
    var status = document.getElementById("check-all").checked;
    var selectedText = `${boxes.length - 1} Selected`;

    if (status){
        $("#actions").show();
    }else{
        $("#actions").hide();
    }


    for (var i = 1; i < boxes.length; i++){
        boxes[i].checked = status;
    }

    var selectedInfo = document.getElementById("infoSelect");

    if (status){
        selectedInfo.innerHTML = selectedText;
    }else{
        selectedInfo.innerHTML = "";
    }
}

function singlebox(e){
    if ($("#actions").css("display") == "none"){
        $("#actions").toggle();
    }
    var status = e.srcElement.checked
    var text = document.getElementById("infoSelect").innerHTML;
    var num = parseInt(text.charAt(0))
    var output = "";

    if (!status){
        if (document.getElementById("check-all").checked){
            document.getElementById("check-all").checked = false;
        }
    }

    if (status){
        if (text == ""){
            output = "1 Selected"
        }else if (text != ""){
            output = `${num + 1} Selected`
        }
    }else{
        if (num == 1){
            $("#actions").toggle();
            output = ""
        }else if (text != ""){
            output = `${num - 1} Selected`
        }
    }
    document.getElementById("infoSelect").innerHTML = output;
    console.log(output)
}

function changeBrnd(e){
    element = $(e);

    $.post("/brand", {
        name: element.prop('id')
    },
    function (data, state) {
        alert("Success");
    })
}

$("#InputPassword").on('keyup', function (e) {
    key = e.originalEvent.key;
    if (key == "Enter"){
        login();
    }
});

function login(e) {
    $.post("/", {
        email: $("#InputEmail").val(),
        password: $("#InputPassword").val(),
        google: e
    },
    function (data, status){
        val = JSON.parse(data);
        if (val.success){
            if (!val.hasOwnProperty("prevURL")){
                var url = "/dashboard";
                $(location).attr('href', url);
            }else{
                var url = "/" + val.prevURL;
                $(location).attr('href', url);
            }
        }else{
            $(".alert-danger").show();
        }
    })
}

$("#resetBtn").on("click", function(e){
    $.post("/reset", {
        email: $("#email").val()
    }, function(data, status){
        response = JSON.parse(data);
        if (response.success){
            $("#email").addClass("is-valid");
            $(".modal-body").html("<p>Check your inbox for the password reset email</p>")
        }else{
            $("#resetForm").removeClass("was-validated")
            $("#email").addClass("is-invalid");
            $("#msg").html(response.message);
        }
    })
})

$("[aria-labelledby='filters']").on("click", function(e){
    id = $(e.target).attr("collection-id");
    
    if (window.location.toString().includes("page") && !window.location.toString().includes("collection")){
        window.location = window.location + "&collection=" + id;
    } else if (window.location.toString().includes("collection")) {
        window.location = window.location.toString().replace(/collection=[\d]*/, "collection=" + id);
    }else{
        window.location = window.location + "?collection=" + id;
    }
})

$("[aria-labelledby='orderby']").on("click", function (e) {
    id = $(e.target).attr("column");

    if (window.location.toString() == (window.origin + window.location.pathname)){
        window.location = window.location + "?orderby=" + id
    } else if (window.location.toString().includes("orderby")){
        window.location = window.location.toString().replace(/orderby=[\w]*/, "orderby=" + id);
    }else{
        window.location = window.location + "&orderby=" + id;
    }
})

$("#delete").on("click", function(){
    if (confirm("Are you sure you want to delete")){
        checkboxes = $(".checkbox-lg:checkbox:checked");

        ids = [];

        for (let i = 0; i < checkboxes.length; i++) {
            const element = checkboxes[i];
            let id = $(element).parent().parent().prop("id");
            if (id != ""){
                ids.push(id);
            }
        }

        $.ajax({
            url: window.origin + window.location.pathname + "/delete", 
            type: "post",
            data: {"ids": JSON.stringify(ids)}, 
            success: function(result){
                window.location.reload();
            },
            error: function(xhr, status, error){
                alert(JSON.parse(xhr.responseText).message);
            }
        })
    }
})

$("#wipe").on("click", function () {
    console.log("hello");
})

$("#remove").on("click", function(){
    if (confirm("Are you sure you want to delete")) {
        $.ajax({
            url: window.origin + window.location.pathname + "/delete",
            type: "post",
            data: { "id": $("#remove").attr("remove-id")},
            success: function (result) {
                window.location.reload();
            },
            error: function (xhr, status, error) {
                $(".alert-danger").show();
                $(".alert-danger").html(JSON.parse(xhr.responseText).message);
            }
        })
    }
})

$("#linkRadio").on("click", function(){
    $("#linkDiv").show();
    $("#fileDiv").hide();
    $("#fileRadio").prop("checked", false);
})
$("#fileRadio").on("click", function () {
    $("#linkDiv").hide();
    $("#fileDiv").show();
    $("#linkRadio").prop("checked", false);
})

$("#items").on("input", function(){
    id = $("#items").val();

    if (window.location.toString().includes("page") && !window.location.toString().includes("items")) {
        window.location = window.location + "&items=" + id;
    } else if (window.location.toString().includes("items")) {
        window.location = window.location.toString().replace(/items=[\d]*/, "items=" + id);
    } else {
        window.location = window.location + "?items=" + id;
    }
})

$("[set-brand]").on("click", function(e){
    e.preventDefault();

    $.post("/brand", {
        default: $(this).attr("set-brand")
    },function(data, state){
        if (state == "success") {
            location.reload()
        }
    })
})

$("[brand-edit-id]").on("click", function(e){
    $("#updateBrand").attr("brand-id", $(e.target).attr("brand-edit-id"));
})

$("#updateBrand").on("click", function(){
    formData = new FormData();

    if ($("#brandIcon")[0].files.length > 0) {
        formData.append("logo", $("#brandIcon")[0].files[0]);
    }

    formData.append("name", $("#brandName").val());
    formData.append("id", $("#updateBrand").attr("brand-id"));

    $.ajax({
        url: "/brand/branding/update",
        type: "post",
        data: formData,
        processData: false,
        contentType: false,
        success: function (result) {
            window.location.reload();
        },
        error: function (xhr, status, error) {
            $(".alert-danger").show();
            $(".alert-danger").html(JSON.parse(xhr.responseText).message);
        }
    })
})

brandName = "";
//Removing brands
$(".remove-brand").on("click", function(){
    brandName = $(".remove-brand").parent().parent().siblings().attr("id");
})

$("#removeBrand").on("click", function(){
    if (brandName == $("#removebrandName").val()){
        $.post("/brand/delete", {
            id: brandName
        }, function(data, status){
            window.location.reload();
        })
    }
})

$("#addBrand").on("click", function(){
    formData = new FormData();

    if ($("#addbrandName").val() != ""){
        $("#error").remove();
        $("#addbrandName").removeClass("is-invalid");

        if ($("#addbrandIcon")[0].files.length > 0) {
            formData.append("logo", $("#addbrandIcon")[0].files[0]);
        }

        formData.append("name", $("#addbrandName").val());
        formData.append("import", $("#importUsers").prop("checked"));

        $.ajax({
            url: "/brand/add",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function (result) {
                window.location.reload();
            },
            error: function (xhr, status, error) {
                $(".alert-danger").show();
                $(".alert-danger").html(JSON.parse(xhr.responseText).message);
            }
        })
    }else{
        $("#addbrandName").addClass("is-invalid");
        $("#addbrandName").parent().parent().append(`<small id="error" class="form-text text-muted">You Must Provide a Brand Name</small>`);
    }
})