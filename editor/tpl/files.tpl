{include file="base.tpl" args=$header}
<main>
    <div class="container row" id="filesList">
        <form class="col s12 m9" method="post" action="" id="live-search">
            <div class="row" id="search">
                <div class="input-field col s12 ">
                    <i class="mddi mddi-file-find prefix grey-text text-darken-2"></i>
                    <input id="filter" type="text" class="validate">
                    <label for="filter">In Dateien suchen ...</label>
                </div>
            </div>
        </form>
        <div class="input-field col s6 m2 offset-m1">
            <select id="sort">
                <option value="idAsc">ID aufstg.</option>
                <option value="idDesc">ID abstg.</option>
                <option value="nameAsc">Name aufstg.</option>
                <option value="nameDesc">Name abstg.</option>
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
                <th data-field="email" width="30%">Details</th>
                <th data-field="actions" width="20%"></th>
            </tr>
            </thead>
            <tbody id="files">
            {for i 0 10}
                <tr>
                    <td><span class="placeholder-big light" style="width: 30px;"></span></td>
                    <td><span class="placeholder-small" style="width: 100%;"></span><br/><span class="placeholder-small light" style="width: 100%;"></span></td>
                    <td><span class="placeholder-small light" style="width: 100%;"></span></td>
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
            <a class="btn-floating btn-large waves-effect waves-light blue" onclick="newFile();">
                <i class="mddi mddi-network-upload"></i>
            </a>
        </div>
    </div>
    <div class="container" id="newFileForm" style="display: none;">
        <div class="card-panel">
            <div class="row">
                <div class="col s12 bolden indigo-text">
                    Neue Datei hochladen
                </div>
                <form class="col s12">
                    <div class="row">
                        <div class="input-field col s12 m6">
                            <input id="new-name" type="text">
                            <label for="new-name">Name</label>
                        </div>
                        <div class="file-field input-field col s12 m6">
                            <div class="btn blue">
                                <span>Datei</span>
                                <input type="file">
                            </div>
                            <div class="file-path-wrapper">
                                <input class="file-path validate" type="text">
                            </div>
                        </div>
                    </div>
                </form>
                <div class="col s12">
                    <a class="waves-effect waves-light btn indigo right" onclick="submitNewFile();">hochladen</a>
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
<script src="js/files.js"></script>
{include file="end.tpl"}