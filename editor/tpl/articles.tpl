{include file="base.tpl" args=$header}
<main>
    <div class="container row" id="articleList">
        <form class="col s12 m9" method="post" action="" id="live-search">
            <div class="row" id="search">
                <div class="input-field col s12 ">
                    <i class="mddi mddi-magnify prefix grey-text text-darken-2"></i>
                    <input id="filter" type="text" class="validate">
                    <label for="filter">In Artikeln suchen ...</label>
                </div>
            </div>
        </form>
        <div class="input-field col s6 m2 offset-m1">
            <select id="sort">
                <option value="idAsc">ID aufstg.</option>
                <option value="idDesc">ID abstg.</option>
                <option value="nameAsc">Name aufstg.</option>
                <option value="nameDesc">Name abstg.</option>
                <option value="dateAsc">Datum aufstg.</option>
                <option value="dateDesc">Datum abstg.</option>
            </select>
            <label>Sortieren nach</label>
        </div>
        <ul id="pages" class="pagination col s12 center center-align">
        </ul>
        <table class="highlight col s12">
            <thead>
            <tr>
                <th data-field="id" width="50px">ID</th>
                <th data-field="name" width="40%">Name</th>
                <th data-field="email" width="30%">Status</th>
                <th data-field="actions" width="20%"></th>
            </tr>
            </thead>
            <tbody id="articles">
            {for i 0 10}
            <tr>
                <td><span class="placeholder-big light" style="width: 30px;"></span></td>
                <td><span class="placeholder-small" style="width: 100%;"></span><br/><span class="placeholder-small light" style="width: 100%;"></span></td>
                <td><span class="placeholder-small light" style="width: 100%;"></span><br/><span class="placeholder-small light" style="width: 100%;"></span></td>
                <td>
                    <span class="placeholder-big right" style="width: 30px;"></span>
                    <span class="placeholder-big right" style="width: 30px;"></span>
                    <span class="placeholder-big right" style="width: 30px;"></span>
                </td>
            </tr>
            {/for}
            </tbody>
        </table>
        <div class="fixed-action-btn">
            <a class="btn-floating btn-large waves-effect waves-light blue" onclick="newArticle();">
                <i class="mddi mddi-note-plus"></i>
            </a>
        </div>
    </div>
    <div class="container" id="newUserForm" style="display: none;">
        <div class="card-panel">
            <div class="row">
                <div class="col s12 bolden indigo-text">
                    Neuen Artikel erstellen
                </div>
                <form class="col s12">
                    <div class="row">
                        <div class="input-field col s6">
                            <input id="new-username" type="text">
                            <label for="new-username">Benutzername</label>
                        </div>
                        <div class="input-field col s6">
                            <input id="new-realname" type="text">
                            <label for="new-realname">Voller Name</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s6">
                            <input id="new-password" type="password" class="validate">
                            <label for="new-password">Passwort</label>
                        </div>
                        <div class="input-field col s6">
                            <input id="new-email" type="email" class="validate">
                            <label for="new-email">Email</label>
                        </div>
                    </div>
                </form>
                <div class="col s12">
                    <a class="waves-effect waves-light btn indigo right" onclick="submitNewUser();">erstellen</a>
                    <a class="waves-effect waves-red btn-flat right" onclick="backToList();">abbrechen</a>
                </div>
            </div>
        </div>
    </div>
    <div class="container" id="editUserForm" style="display: none;">
        <div class="card-panel">
            <div class="row">
                <div class="col s12 bolden indigo-text">
                    Benutzer bearbeiten
                </div>
                <form class="col s12">
                    <div class="row">
                        <div class="input-field col s6">
                            <input id="edit-username" type="text" disabled>
                            <label for="edit-username">Benutzername</label>
                        </div>
                        <div class="input-field col s6">
                            <input id="edit-realname" type="text">
                            <label for="edit-realname">Voller Name</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="input-field col s6">
                            <input id="edit-password" type="password" class="validate">
                            <label for="edit-password">Passwort</label>
                        </div>
                        <div class="input-field col s6">
                            <input id="edit-email" type="email" class="validate">
                            <label for="edit-email">Email</label>
                        </div>
                    </div>
                </form>
                <div class="col s12">
                    <a class="waves-effect waves-light btn indigo right" onclick="submitEditUser();">speichern</a>
                    <a class="waves-effect waves-red btn-flat right" onclick="backToList();">abbrechen</a>
                </div>
            </div>
        </div>
    </div>
</main>
<script>
    $(document).ready(function() {
        $('select').material_select();
    });
</script>
<script src="js/articles.js"></script>
{include file="end.tpl"}