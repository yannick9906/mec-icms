/**
 * Created by yanni on 2017-03-19.
 */

let sortName = "#sort";
let listName = "#entries"
let linkList = "../api/calendar/getList.php";
let jsonField = "entries"
let pagesize = 12;
///////////////////////////////////////////////////////////////////////
// TODO Fill List Template and update() method
let listElemTmplt = `
    <tr id="row-{{i}}" style="display: none;">
        <td>{{id}}</td>
        <td><b>{{name}}</b> ({{{dateParsed}}})<br/>erstellt von {{authorReal}}</td>
        <td>Version {{version}} <i class="{{{stateCSS}}}"></i> <span class="{{{color}}}">{{stateText}}</span><br/>von {{lastEditAuthor}} - {{lastEdit}}</td>
        <td>
        <a id="del{{id}}" onclick="del({{{id}}})" href="#!" style="padding-left:10px;padding-right:10px;" class="btn-flat right red-text tooltipped anim" data-position="top" data-delay="50" data-tooltip="Löschen"><i class="mddi mddi-delete"></i></a>
        <a id="denydel{{id}}" onclick="denydelete({{{id}}})" href="#!" style="padding-left:10px;padding-right:10px;display:none;" class="btn-flat right red-text tooltipped anim"><i class="mddi mddi-close"></i></a>
        <a id="confdel{{id}}" onclick="confdelete({{{id}}})" href="#!" style="padding-left:10px;padding-right:10px;display:none;" class="btn-flat right green-text tooltipped anim"><i class="mddi mddi-check"></i></a>
        <a onclick="history({{id}})" href="#!" style="padding-left:10px;padding-right:10px;" class="btn-flat right tooltipped anim" data-position="top" data-delay="50" data-tooltip="Versionsverlauf"><i class="mddi mddi-history"></i></a>
        <a href="calendar.php?edit={{vId}}" style="padding-left:10px;padding-right:10px;" class="btn-flat right tooltipped anim" data-position="top" data-delay="50" data-tooltip="Bearbeiten"><i class="mddi mddi-pencil"></i></a>
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
let sort = "idAsc";
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
            data = JSON.stringify(list);
            for(let i = 0; i < list.length; i++) {
                let e = list[i];
                e.i = i;
                e.color = e.stateCSS.split(" ")[0];
                if(e.dateUntil != null)
                    e.dateParsed = e.date+" - "+e.dateUntil;
                else e.dateParsed = e.date
                $(listName).append(template(e));
                size = i;
            }
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
    $("#newEntryForm").fadeOut(200, function() {
        $("#entryList").fadeIn(200);
    });
    currEdit = -1;
}

function newEntry() {
    $("#new-name").val("");
    $("#new-date").val("");
    $("#new-dateUntil").val("");
    $("#entryList").fadeOut(200, function() {
        $("#newEntryForm").fadeIn(200);
    });
}

function submitNewEntry() {
    let data = {
        name: $("#new-name").val(),
        date: $("#new-date").val(),
        dateUntil: $("#new-dateUntil").val()=="" ? null : $("#new-dateUntil").val()
    };
    $.post("../api/calendar/create.php", data, function(response) {
        let json = JSON.parse(response);
        if(json.success == true) {
            Materialize.toast("Eintrag erstellt", 1000, "green");
            backToList();
        } else {
            Materialize.toast("Es ist ein Fehler aufgetreten", 2000, "red");
        }
    });
}

function del(id) {
    $("#del"+id).hide();
    $("#confdel"+id).show();
    $("#denydel"+id).show();
}

function confdelete(id) {
    $("#del"+id).show();
    $("#confdel"+id).hide();
    $("#denydel"+id).hide();
    let data = {
        cID: id
    };
    $.getJSON("../api/calendar/delete.php", data, function(json) {
        if(json.success == true) {
            Materialize.toast("Eintrag gelöscht", 1000, "green");
        } else {
            Materialize.toast("Es ist ein Fehler aufgetreten", 2000, "red");
        }
        updateData();
        updatePages();
    });
}

function denydelete(id) {
    $("#del"+id).show();
    $("#confdel"+id).hide();
    $("#denydel"+id).hide();
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

    $(".datetimepicker").bootstrapMaterialDatePicker({
        format: 'YYYY-MM-DDTHH:mm:00',
        lang: 'de',
        weekStart: 1,
        cancelText: 'ABBRECHEN',
        switchOnClick: true,
    });
});