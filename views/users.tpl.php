{% include "global/head.tpl.php" %}
<div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title">Users || Total likes: {{sumLikes}} || Total users: {{ totalRows }}</h3>
    </div>
    <div class="panel-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <form method="get">
                        <th></th>
                        <th></th>
                        <th><select class="form-control" name="userGroup">
                                <option value="">all</option>
                                {% for group in groups %}
                                <option value="{{ group.id }}">{{ group.name }}</option>
                                {% endfor %}
                            </select></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th><select class="form-control" name="LogIn">
                                <option value="">all</option>
                                <option value="0">LogIn 0</option>
                                <option value="1">LogIn 1</option>
                            </select></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th>
                            <select class="form-control" name="ban">
                                <option value="">all</option>
                                <option value="0">No ban</option>
                                <option value="1">Ban</option>
                            </select></th>
                        <th>
                            <button type="submit" class="btn btn-primary">Filter</button>
                        </th>
                    </form>
                </tr>
                </thead>
                <thead>
                <tr>
                    <th>#</th>
                    <th><a href="?orderBy=id&sort=desc">id</a></th>
                    <th><a href="?orderBy=userGroup&sort=desc">userGroup</a></th>
                    <th><a href="?orderBy=userName&sort=desc">userName</a></th>
                    <th><a href="?orderBy=firstName&sort=desc">firstName</a></th>
                    <th><a href="?orderBy=email&sort=desc">email</a></th>
                    <th><a href="?orderBy=logIn&sort=desc">logIn</a></th>
                    <th><a href="?orderBy=proxy&sort=desc">proxy</a></th>
                    <th><a href="?orderBy=requests&sort=desc">requests</a></th>
                    <th><a href="?orderBy=follows&sort=desc">follows</a></th>
                    <th><a href="?orderBy=likes&sort=desc">likes</a></th>
                    <th><a href="?orderBy=dateCreate&sort=desc">dateCreate</a></th>
                    <th><a href="?orderBy=hour&sort=desc">hour</a></th>
                    <th><a href="?orderBy=ban&sort=desc">ban</a></th>
                    <th>options</th>
                </tr>
                </thead>
                <tbody>
                {% for user in users %}
                {% if user.ban %}
                <tr class="danger">
                    {% elseif user.ban == 0 and user.logIn == 1 %}
                <tr class="success">
                    {% else %}
                <tr>
                    {% endif %}
                    <td><input type="checkbox" value="{{ user.id }}"></td>
                    <td>{{ user.id }}</td>
                    <td>{{ user.userGroup }}</td>
                    <td><a href="https://instagram.com/{{ user.userName }}" target="_blank">{{ user.userName }}</a></td>
                    <td>{{ user.firstName }}</td>
                    <td>{{ user.email }}</td>
                    <td>{{ user.logIn }}</td>
                    <td>{{ user.proxy }}</td>
                    <td>{{ user.requests }}</td>
                    <td>{{ user.follows }}</td>
                    <td>{{ user.likes }}</td>
                    <td>{{ user.dateCreate }}</td>
                    <td>{{ user.hour }}</td>
                    <td>{{ user.ban }}</td>
                    <td><a href="/deleteUsers?id={{ user.id }}"><span class="glyphicon glyphicon-trash"></a></span></td>
                </tr>
                {% endfor %}
                </tbody>
            </table>
        </div>
    </div>
    <div class="panel-footer">
        <button class="btn btn-danger">Delete</button>
        <button class="btn btn-success" id="checkAll">check All</button>
        <button class="btn btn-primary" id="addGroup" data-toggle="modal" onclick="check()"
                data-target=".bs-example-modal-sm">Add to Group
        </button>
        <ul class="pagination pull-right" style="margin: 0;">
            {% for i in range(1, totalPages) %}
            <li
                    {% if i==setPage %} class="active" {% endif %}><a
                        href="/users?page={{ i }}">{{ i }} {% if i==setPage %}<span class="sr-only">(current)</span>{%
                    endif
                    %}</a></li>
            {% endfor %}
        </ul>
    </div>
</div>

<div class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <form method="post">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="gridSystemModalLabel">Add profile to group</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <select class="form-control input-lg" name="userGroup">
                            {% for group in groups %}
                            <option value="{{ group.id }}">{{ group.name }}</option>
                            {% endfor %}
                        </select>
                        <input type="hidden" class="form-control" id="id_to_group" name="id">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    function check() {
        var checkboxes = $('input[type=checkbox]:checked');
        var id = [];
        for (var i = 0; i < checkboxes.length; i++) {
            id.push(checkboxes[i].value);
        }
        console.log(id);
        $('#id_to_group').val(id);
    }

    $('#checkAll').click(function () {
        $('input').prop('checked', true);
    });

</script>
{% include "global/footer.tpl.php" %}
