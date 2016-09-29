{include file="base.tpl" args=$header}
<main>
    <div class="container row">
        <form class="col s12 m9" method="post" action="" id="live-search">
            <div class="row" id="search">
                <div class="input-field col s12 ">
                    <i class="mddi mddi-account-search prefix grey-text text-darken-2"></i>
                    <input id="filter" type="text" class="validate">
                    <label for="filter">In Benutzer suchen ...</label>
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
                <th data-field="id">ID</th>
                <th data-field="name">Name</th>
                <th data-field="email">Email</th>
            </tr>
            </thead>
            <tbody id="users">
            <tr>
                <td colspan="3" class="grey-text center"><i>Elemente werden geladen...</i></td>
            </tr>
            <!--<tr>
                <td>1</td>
                <td>Yannick Félix <span class="grey-text">[yannick]</span></td>
                <td>yannick.felix1999@gmail.com</td>
            </tr>-->
            </tbody>
        </table>
    </div>
</main>
<script>
    $(document).ready(function() {
        $('select').material_select();
    });
</script>
<script src="js/users.js"></script>
{include file="end.tpl"}