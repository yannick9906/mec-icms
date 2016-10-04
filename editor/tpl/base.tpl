<!DOCTYPE html>
<html>
<head>
    <title>MEC ICMS - {$args.title}</title>
    <meta charset="utf-8" />
    <!--Import Google Icon Font-->
    <!--<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">-->
    <link href='https://fonts.googleapis.com/css?family=Roboto+Condensed:400,700' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Roboto+Mono' rel='stylesheet' type='text/css'>
    <!--Import materialize.css-->
    <link type="text/css" rel="stylesheet" href="../libs/materialize/css/materialize.css"  media="screen,projection"/>
    <link type="text/css" rel="stylesheet" href="../css/style.css" />
    <link type="text/css" rel="stylesheet" href="../css/materialdesignicons.min.css" media="all"/>

    <!--Let browser know website is optimized for mobile-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link rel="manifest" href="../manifest.json" />
    <meta name="mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="theme-color" content="#4286f4" />
</head>
<body>
<!--Import jQuery before materialize.js-->
<script type="text/javascript" src="../libs/jquery-2.2.1.min.js"></script>
<script type="text/javascript" src="../libs/materialize/js/materialize.min.js"></script>
<script type="text/javascript" src="../libs/handlebars.js"></script>
<script type="text/javascript" src="../libs/md5.js"></script>


<!-- Dropdown Structure -->
<ul id="dropdown1" class="dropdown-content">
    <li><a href="users.php?action=edit&uID={$args.uID}">Mein Account</a></li>
    <li class="divider"></li>
    <li><a href="login.php?err=3">Abmelden</a></li>
</ul>
<div class="navbar-fixed">
    <nav class="blue">
        <div class="nav-wrapper">
            <a href="#!" class="brand-logo hide-on-med-and-down" style="padding-left: 310px;">ICMS - Mainzer Eissport Club</a>
            <a href="#!" class="brand-logo hide-on-large-only">ICMS</a>
            <ul id="slide-out" class="side-nav fixed">
                <li><div class="userView">
                        <img class="background" src="../png/background.jpg">
                        <a href="#!"><div class="circle red center white-text" style="font-size: 40px; line-height: 65px;">{$args.usrchar}</div></a>
                        <a href="#!"><span class="white-text name" style="text-shadow: 1px 1px 5px black;">{$args.realname}</span></a>
                        <a href="#!"><span class="white-text email" style="text-shadow: 1px 1px 5px black;">{$args.email}</span></a>
                    </div></li>
                <li id="nav-account"><a href="#!"><i class="material-icons">person</i>Account</a></li>
                <li id="nav-dashboard"><a href="dashboard.php">Dashboard</a></li>
                <li class="sub" id="nav-logout"><a href="login.php?err=3">Logout</a></li>
                <li><div class="divider"></div></li>
                <li class="subheader"><a class="subheader indigo-text bolden">Seiten</a></li>
                <li class="sub" id="nav-special"><a href="#!">Spezial</a></li>
                <li class="sub" id="nav-article"><a href="#!">Artikel</a></li>
                <li class="sub" id="nav-news"><a href="#!">News</a></li>
                <li class="sub" id="nav-competition"><a href="#!">Wettkämpfe</a></li>
                <li class="sub" id="nav-event"><a href="#!">Termine</a></li>
                <li><div class="divider"></div></li>
                <li><a class="subheader indigo-text bolden">Dateien</a></li>
                <li class="sub" id="nav-files"><a href="#!">Dateien</a></li>
                <li class="sub" id="nav-download"><a href="#!">Downloads</a></li>
                <li class="sub" id="nav-pictures"><a href="#!">Bilder</a></li>
                <li><div class="divider"></div></li>
                <li><a class="subheader indigo-text bolden">Administration</a></li>
                <li class="sub" id="nav-database"><a href="../adminer/">Datenbank</a></li>
                <li class="sub" id="nav-users"><a href="users.php">Benutzer</a></li>
                <li><div class="divider"></div></li>
            </ul>
            <a href="#" data-activates="slide-out" class="button-collapse"><i class="material-icons">menu</i></a>
        </div>
        <script>
            $(document).ready(function() {
                $("#nav-{$args.highlight}").addClass("active");
            });
        </script>
    </nav>
</div>