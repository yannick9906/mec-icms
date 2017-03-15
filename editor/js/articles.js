/**
 * Created by yanni on 2017-03-11.
 */

let sortName = "#sortCurr";
let listName = "#articles"
let linkList = "../api/articles/getList.php";
let jsonField = "articles"
let pagesize = 12;
///////////////////////////////////////////////////////////////////////
// TODO Fill List Template and update() method
let listElemTmplt = `
    <tr id="row-{{i}}" style="display: none;">
        <td>{{id}}</td>
        <td><b>{{name}}</b><br/>erstellt von {{authorReal}}</td>
        <td>Version {{version}} <i class="{{{stateCSS}}}"></i> <span class="{{{color}}}">{{stateText}}</span><br/>von {{lastEditAuthor}} - {{lastEdit}}</td>
        <td>
        <a onclick="delete({{id}})" href="#!" style="padding-left:10px;padding-right:10px;" class="btn-flat right red-text tooltipped" data-position="top" data-delay="50" data-tooltip="Löschen"><i class="mddi mddi-delete"></i></a>
        <a onclick="history({{id}})" href="#!" style="padding-left:10px;padding-right:10px;" class="btn-flat right tooltipped" data-position="top" data-delay="50" data-tooltip="Versionsverlauf"><i class="mddi mddi-history"></i></a>
        <a href="articles.php?edit={{vId}}" style="padding-left:10px;padding-right:10px;" class="btn-flat right tooltipped" data-position="top" data-delay="50" data-tooltip="Bearbeiten"><i class="mddi mddi-pencil"></i></a>
        </td>
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
let sort = "ascID";
var data = "";
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
                e.i = i;
                e.color = e.stateCSS.split(" ")[0];
                $(listName).append(template(e));
                size = i;
                delete e.i;
                delete e.color;
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
        $('.tooltipped').tooltip({delay: 50});
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

function newArticle() {
    $("#new-username").removeClass("invalid");
    $("#new-username").val("");
    $("#new-realname").val("");
    $("#new-password").val("");
    $("#new-email").val("");
    $("#userList").fadeOut(200, function() {
        $("#newUserForm").fadeIn(200);
    });
}

function submitNewArticle() {
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