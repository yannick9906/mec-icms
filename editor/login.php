<?php
    if($_GET["err"] == 3) {
        session_destroy();
    }
?>
<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="UTF-8">
        <title>Login</title>
        <link href='https://fonts.googleapis.com/css?family=Roboto+Condensed:400,700' rel='stylesheet' type='text/css'>
        <link href='https://fonts.googleapis.com/css?family=Roboto+Mono' rel='stylesheet' type='text/css'>
        <!--Import materialize.css-->
        <link type="text/css" rel="stylesheet" href="../libs/materialize/css/materialize.min.css"  media="screen,projection"/>
        <link type="text/css" rel="stylesheet" href="../css/style.css" />
        <link type="text/css" rel="stylesheet" href="../css/materialdesignicons.min.css" media="all"/>

        <!--Let browser know website is optimized for mobile-->
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <link rel="manifest" href="../manifest.json" />
        <meta name="mobile-web-app-capable" content="yes" />
        <meta name="apple-mobile-web-app-capable" content="yes" />
        <meta name="theme-color" content="#3F51B5" />
    </head>
    <body>
        <script type="text/javascript" src="../libs/jquery-2.2.1.min.js"></script>
        <script type="text/javascript" src="../libs/materialize/js/materialize.min.js"></script>
        <script type="text/javascript" src="../libs/md5.js"></script>
        <style>
            .bg {
                background: url("../css/pattern.png") no-repeat center;
                background-size: 100% 100%;
            }
        </style>

    <div id="bg" class="bg valign-wrapper" style="position: absolute; height: 100%; width: 100%;">
        <div class="valign center" style="width: 100%;">
            <div class="card-panel row left-align" style="display: inline-block">
                <span class="bolden indigo-text col s6">ICMS LOGIN</span><span class="right-align grey-text text-lighten-2 col s6">ICMS(MEC) v4.0a</span><br/><br/>
                <div id="loginFields">
                    <div class="input-field col s12">
                        <i class="material-icons prefix">account_circle</i>
                        <input id="username" type="text" required data-error="Benutzername existiert nicht">
                        <label for="username">Benutzername</label>
                    </div>
                    <div class="input-field col s12">
                        <i class="material-icons prefix">lock</i>
                        <input id="password" type="password" required data-error="Passwort falsch">
                        <label for="password">Passwort</label>
                        <br/><br/>
                    </div>
                    <a href="../" class="btn btn-flat indigo-text col s2 offset-s5"><i class="material-icons large">clear</i></a>
                    <button class="btn waves-effect waves-light indigo col s4 offset-s1" onclick="doLogin()">
                        <i class="material-icons">send</i>
                    </button>
                </div>
                <div id="loading" style="display: none;" class="center col s12">
                    <div class="preloader-wrapper big active">
                        <div class="spinner-layer spinner-blue-only">
                            <div class="circle-clipper left">
                                <div class="circle"></div>
                            </div><div class="gap-patch">
                                <div class="circle"></div>
                            </div><div class="circle-clipper right">
                                <div class="circle"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col s12 center" style="display: none;" id="success">
                    <i class="material-icons large green-text">check</i>
                </div>
            </div>
        </div>
    </div>
    </body>
    <script>
        var usernameField =  $("#username");
        var passwordField =  $("#password");

        $(document).ready(function() {
            if(<? if(isset($_GET["err"])) echo $_GET["err"]; else echo 0; ?> == 1) {
                Materialize.toast("Kennwort falsch", 2000, "red");
                usernameField.removeClass("valid");
                usernameField.addClass("invalid");
            } else if(<? if(isset($_GET["err"])) echo $_GET["err"]; else echo 0; ?> == 2) {
                Materialize.toast("Benutzername falsch", 2000, "red");
                passwordField.removeClass("valid");
                passwordField.addClass("invalid");
            } else if(<? if(isset($_GET["err"])) echo $_GET["err"]; else echo 0; ?> == 3) {
                Materialize.toast("Logout erfolgreich", 2000, "green");
            } else {
                passwordField.removeClass("valid");
                passwordField.removeClass("invalid");
                usernameField.removeClass("valid");
                usernameField.removeClass("invalid");
            }

            document.querySelector("#password").addEventListener('keypress', function(e) {
                var key = e.which || e.keyCode;
                if(key === 13) {
                    doLogin();
                }
            })
        });

        function doLogin() {
            username = usernameField.val();
            password = passwordField.val();
            passhash = md5(password);

            $("#loginFields").fadeOut(500, function() {
                $("#loading").fadeIn(500);
                $.getJSON("../api/users/checkUsername.php?username="+username, null, function(json) {
                    if(json["exists"] == 1) {
                        $.getJSON("../api/users/tryLogin.php?username="+username+"&passhash="+passhash, null, function(json2) {
                            if(json2["success"] == 1) {
                                $("#loading").fadeOut(250, function() {
                                    $("#success").fadeIn(250, function() {
                                        $("#bg").fadeOut(250, function() {
                                            window.location.href = "./dashboard.php";
                                        });
                                    })
                                });
                            } else {
                                Materialize.toast("Kennwort falsch", 2000, "red");
                                passwordField.removeClass("valid");
                                passwordField.addClass("invalid");
                                $("#loading").fadeOut(500, function() {
                                    $("#loginFields").fadeIn(500);
                                });
                            }
                        })
                    } else {
                        Materialize.toast("Benutzername falsch", 2000, "red");
                        usernameField.removeClass("valid");
                        usernameField.addClass("invalid");
                        $("#loading").fadeOut(500, function() {
                            $("#loginFields").fadeIn(500);
                        });
                    }
                });
            });
        }
    </script>
</html>