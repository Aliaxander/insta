{% include "global/head.tpl.php" %}
<div class="table-responsive">
    <table class="table">
        <thead>
        <tr>
            <th>#</th>
            <th><a href="?orderBy=id&sort=desc">id</a></th>
            <th><a href="?orderBy=userName&sort=desc">userName</a></th>
            <th><a href="?orderBy=firstName&sort=desc">firstName</a></th>
            <th><a href="?orderBy=email&sort=desc">email</a></th>
            <th><a href="?orderBy=password&sort=desc">password</a></th>
            <th><a href="?orderBy=logIn&sort=desc">logIn</a></th>
            <th><a href="?orderBy=proxy&sort=desc">proxy</a></th>
            <th><a href="?orderBy=requests&sort=desc">requests</a></th>
            <th><a href="?orderBy=follows&sort=desc">follows</a></th>
            <th><a href="?orderBy=likes&sort=desc">likes</a></th>
            <th><a href="?orderBy=dateCreate&sort=desc">dateCreate</a></th>
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
            <td><a href="https://instagram.com/{{ user.userName }}" target="_blank">{{ user.userName }}</a></td>
            <td>{{ user.firstName }}</td>
            <td>{{ user.email }}</td>
            <td>{{ user.password }}</td>
            <td>{{ user.logIn }}</td>
            <td>{{ user.proxy }}</td>
            <td>{{ user.requests }}</td>
            <td>{{ user.follows }}</td>
            <td>{{ user.likes }}</td>
            <td>{{ user.dateCreate }}</td>
            <td><a href="/deleteUsers?id={{ user.id }}"><span class="glyphicon glyphicon-trash"></a></span></td>
        </tr>
        {% endfor %}
        </tbody>
    </table>
    <button class="btn btn-danger" onclick="del()">Delete</button>
    <ul class="pagination pull-right" style="margin: 0;">
        {% for i in range(1, totalPages) %}
        <li{% if i==setPage %} class="active" {% endif %}><a
                href="/users?page={{ i }}">{{ i }} {% if i==setPage %}<span class="sr-only">(current)</span>{% endif %}</a></li>
        {% endfor %}
    </ul>
</div>
<script>
    function del() {
        var checkboxes = $('input[type=checkbox]:checked');
        var url = "/deleteUsers?id=";
        for (var i=0; i < checkboxes.length; i++){
            url = url + checkboxes[i].value + ",";
        }
        $.get( url );
        location.reload();
    }

</script>
{% include "global/footer.tpl.php" %}