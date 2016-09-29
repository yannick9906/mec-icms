/**
 * Created by yanni on 29.09.2016.
 */

var filterName = "#filterCurr";
var sortName = "#sortCurr";
var linkList = "getLists.php?action=citizenSimple";
var linkDetail = "citizen.php?action=citizeninfosimple";
var pagesize = 12;
///////////////////////////////////////////////////////////////////////
var listElemTmplt = `
    <tr id="row-{{i}}" style="display: none;">
        <td>{{id}}</td>
        <td>{{name}} <span class="grey-text">[{{usrname}}]</span></td>
        <td>{{email}}</td>
    </tr>
    `;
var template = Handlebars.compile(listElemTmplt);
///////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////
var searchString = "";
var currPage = 1;
var reqPage = 1;
var maxPages = 1;
var size = 0;
var sort = "ascID";
var data = "";
///////////////////////////////////////////////////////////////////////

function setPage(apage) {
    reqPage = apage;
}

function updatePages() {
    if(currPage > maxPages) reqPage = maxPages;
    if(reqPage == 0) reqPage = 1;

    var nextPage = parseInt(currPage)+1;
    var prevPage = currPage-1;
    var p = $("#pages");
    p.html("");
    p.append("<div id='pagesPre' class='col s1'></div>");
    p.append("<div id='pagesSuf' class='col push-s10 s1'></div>");
    p.append("<div id='pagesNum' class='col pull-s1 s10'></div>");

    if(currPage <= 1) $("#pagesPre").append("<li class=\"disabled\"><a><i class=\"material-icons\">chevron_left</i></a></li>");
    else $("#pagesPre").append("<li class=\"waves-effect\"><a onclick=\"setPage("+prevPage+")\"><i class=\"material-icons\">chevron_left</i></a></li>");

    for(var i = 1; i <= maxPages; i++) {
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
    sort = $("#sort").val();
    $.getJSON("../api/users/getList.php?page="+reqPage+"&sort="+sort+"&search="+searchString,null, function(json) {
        maxPages = json['maxPage'];
        currPage = json['page'];
        size = json['size'];
        var list = json['users'];

        if(JSON.stringify(list) != data) {
            $("#users").html("");
            list.forEach(function (e, i, a) {
                $("#users").append(template({i: i,id: e.uID, usrname: e.username, name: e.realname, email: e.email}))
                size = i;
            });
            animate(0);
            data = JSON.stringify(list);
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

var delay = (function(){
    var timer = 0;
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