/**
 * Created by yanni on 29.09.2016.
 */

let sortName = "#sort";
let listName = "#users"
let linkList = "../api/users/getList.php";
let jsonField = "users"
let pagesize = 12;
///////////////////////////////////////////////////////////////////////
// TODO Fill List Template and update() method
let listElemTmplt = `
    <tr id="row-{{i}}" style="display: none;" onclick="editUser({{id}})" class="clickable">
        <td>{{id}}</td>
        <td>{{name}} <span class="grey-text">[{{usrname}}]</span></td>
        <td>{{email}}</td>
    </tr>
    `;
let template = Handlebars.compile(listElemTmplt);
///////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////
let searchString = "";
let currPage = 1;
let reqPage = 1;
let maxPages = 1;
let size = 0;
let sort = "idAsc";
let data = "";
let currEdit = -1;
///////////////////////////////////////////////////////////////////////

function setPage(apage) {
    reqPage = apage;
}

function updatePages() {
    if(currPage > maxPages) reqPage = maxPages;
    if(reqPage == 0) reqPage = 1;

    let nextPage = parseInt(currPage)+1;
    let prevPage = currPage-1;
    let p = $("#pages");
    p.html("");
    p.append("<div id='pagesPre' class='col s1'></div>");
    p.append("<div id='pagesSuf' class='col push-s10 s1'></div>");
    p.append("<div id='pagesNum' class='col pull-s1 s10'></div>");

    if(currPage <= 1) $("#pagesPre").append("<li class=\"disabled\"><a><i class=\"material-icons\">chevron_left</i></a></li>");
    else $("#pagesPre").append("<li class=\"waves-effect\"><a onclick=\"setPage("+prevPage+")\"><i class=\"material-icons\">chevron_left</i></a></li>");

    for(let i = 1; i <= maxPages; i++) {
        if(i != currPage) {
            $("#pagesNum").append("<li class=\"waves-effect\"><a onclick=\"setPage("+i+")\">"+i+"</a></li>");
        } else {
            $("#pagesNum").append("<li class=\"active indigo\"><a onclick=\"setPage("+i+")\">"+i+"</a></li>");
        }
    }

    if(currPage >= maxPages) $("#pagesSuf").append("<li class=\"disabled\"><a><i class=\"material-icons\">chevron_right</i></a></li>");
    else $("#pagesSuf").append("<li class=\"waves-effect\"><a onclick=\"setPage("+nextPage+")\"><i class=\"material-icons\">chevron_right</i></a></li>");
}

function updateData() {
    let sort = $(sortName).val();
    let postdata = {
        page: reqPage,
        sort: sort,
        search: searchString,
        pagesize: pagesize
    }
    $.getJSON(linkList,postdata, function(json) {
        maxPages = json['maxPage'];
        currPage = json['page'];
        size = json['size'];
        let list = json[jsonField];

        if(JSON.stringify(list) != data) {
            $(listName).html("");
            for(let i = 0; i < list.length; i++) {
                let e = list[i];
                $(listName).append(template({i: i,id: e.uID, usrname: e.username, name: e.realname, email: e.email}))
                size = i;
            }
            data = JSON.stringify(list);

            animate(0);
        }
    });
}

function animate(i) {
    if(i <= size) {
        $("#row-"+i).fadeIn(150);
        window.setTimeout("animate("+(i+1)+")", 150);
    }
}

function updateCaller() {
    updateData();
    updatePages();
    window.setTimeout("updateCaller()", 1000);
}

///////////////////////////////////////
function backToList() {
    $("#editUserForm").fadeOut(200, function() {
        $("#userList").fadeIn(200);
    });
    $("#newUserForm").fadeOut(200);
    currEdit = -1;
}

function newUser() {
    $("#new-username").removeClass("invalid");
    $("#new-username").val("");
    $("#new-realname").val("");
    $("#new-password").val("");
    $("#new-email").val("");
    $("#userList").fadeOut(200, function() {
        $("#newUserForm").fadeIn(200);
    });
}

function submitNewUser() {
    data = {
        username: $("#new-username").val(),
        realname: $("#new-realname").val(),
        passhash: md5($("#new-password").val()),
        email: $("#new-email").val()
    };
    $.post("../api/users/create.php", data, function(response) {
        let json = JSON.parse(response);
        if(json.success == "1") {
            Materialize.toast("Benutzer erstellt", 2000, "green");
            backToList();
        } else {
            if(json.error == "missing fields") {
                Materialize.toast("Bitte alle Felder ausfüllen", 2000, "red");
            } else if(json.error == "username exists") {
                Materialize.toast("Der Benutzername existiert bereits", 2000, "red");
                $("#new-username").addClass("invalid");
            }
        }
    });
}

function editUser(id) {
    currEdit = id;
    $.getJSON("../api/users/details.php?id="+id,null, function(json) {
        $("#edit-username").val(json.username);
        $("#edit-realname").val(json.realname);
        $("#edit-password").val("");
        $("#edit-email").val(json.email);
        Materialize.updateTextFields();
        $("#userList").fadeOut(200, function() {
            $("#editUserForm").fadeIn(200);
        });
    })
}

function submitEditUser() {
    let password = $("#edit-password").val();
    let passhash = "NOUPDATE";
    if(password != "") {
        passhash = md5(password)
    };

    data = {
        realname: $("#edit-realname").val(),
        passhash: passhash,
        email: $("#edit-email").val()
    };

    $.post("../api/users/update.php?id="+currEdit, data, function(response) {
        let json = JSON.parse(response);
        if(json.success == "1") {
            Materialize.toast("Benutzer aktualisiert", 2000, "green");
            backToList();
        } else {
            if(json.error == "missing fields") {
                Materialize.toast("Bitte alle Felder ausfüllen", 2000, "red");
            }
        }
    });
}
///////////////////////////////////////

var delay = (function(){
    let timer = 0;
    return function(callback, ms){
        clearTimeout (timer);
        timer = setTimeout(callback, ms);
    };
})();

$(document).ready(function() {
    window.setTimeout("updateCaller()", 500);
    $("#filter").keyup(function () {
        delay(function(){
            searchString = $("#filter").val();
            data = "";
            reqPage = 1;
            updateData();
            updatePages();
        }, 500 );
    });
});