
$("#document").ready(function(){
    var password = "";
    var length = false;
    var number = false;
    var capital = false;
    var symbol = false;

    $("#password").keyup(function(){
        password = $("#password").val();
        if (password.length >= 8){
            $("#chars").addClass("is-valid");
            $("#chars").attr("checked", true);
            $("#chars").parent().css("font-weight", "bold");
            length = true;
        }else{
            $("#chars").removeClass("is-valid");
            $("#chars").attr("checked", false);
            $("#chars").parent().css("font-weight", "normal");
            length = false;
        }

        if (/\d/.test(password)){
            $("#number").addClass("is-valid");
            $("#number").attr("checked", true);
            $("#number").parent().css("font-weight", "bold");
            number = true;
        }else{
            $("#number").removeClass("is-valid");
            $("#number").attr("checked", false);
            $("#number").parent().css("font-weight", "normal");
            number = false;
        }

        if (/[A-Z]/.test(password)){
            $("#capital").addClass("is-valid");
            $("#capital").attr("checked", true);
            $("#capital").parent().css("font-weight", "bold");
            capital = true;
        }else{
            $("#capital").removeClass("is-valid");
            $("#capital").attr("checked", false);
            $("#capital").parent().css("font-weight", "normal");
            capital = false;
        }

        if (/\W/.test(password)) {
            $("#symbol").addClass("is-valid");
            $("#symbol").attr("checked", true);
            $("#symbol").parent().css("font-weight", "bold");
            symbol = true;
        }else{
            $("#symbol").removeClass("is-valid");
            $("#symbol").attr("checked", false);
            $("#symbol").parent().css("font-weight", "normal");
            symbol = false;
        }
    })

    $("#confirmPassword").keyup(function(){
        confirmPassword = $("#confirmPassword").val();
        if (confirmPassword == password){
            $("#confirmPassword").addClass("is-valid");
            $("#password").addClass("is-valid");

            if(length && number && capital && symbol){
                $("#submit").attr("disabled", false);
            }
        }
    })

    var canViewPass = false;
    $("#passwordView").click(function(){
        if (canViewPass){
            $("#password").attr("type", "password");
            $("#passwordView").removeClass("bi-eye-slash");
            $("#passwordView").addClass("bi-eye");
        }else{
            $("#password").attr("type", "text");
            $("#passwordView").removeClass("bi-eye");
            $("#passwordView").addClass("bi-eye-slash");
        }
        canViewPass = !canViewPass;
    })

    var canViewConf = false;
    $("#confirmView").click(function () {
        if (canViewConf) {
            $("#confirmPassword").attr("type", "password");
            $("#confirmView").removeClass("bi-eye-slash");
            $("#confirmView").addClass("bi-eye");
        } else {
            $("#confirmPassword").attr("type", "text");
            $("#confirmView").removeClass("bi-eye");
            $("#confirmView").addClass("bi-eye-slash");
        }
        canViewConf = !canViewConf;
    })

    $("#submit").on("click", function(){
        const regex = /reset\/(.*)/gm;

        $.post("/reset/update", {
            password: $("#password").val(),
            // key: regex.exec(window.location.href)[1]
            key: key
        }, function(data, result){
            response = JSON.parse(data);
            if (response.success){
                window.location.href = "/dashboard";
            }else{

            }
        })
    })
})