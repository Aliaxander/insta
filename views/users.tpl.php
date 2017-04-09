{% include "global/head.tpl.php" %}
<div class="table-responsive">
    <table class="table">
        <thead>
        <tr>
            <th>#</th>
            <th>id</th>
            <th>userName</th>
            <th>firstName</th>
            <th>email</th>
            <th>password</th>
            <th>logIn</th>
            <th>proxy</th>
            <th>requests</th>
            <th>follows</th>
            <th>likes</th>
            <th>dateCreate</th>
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
            <td><input type="checkbox" name="users[]" value="{{ user.id }}"></td>
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
    <ul class="pagination pull-right" style="margin: 0;">
        {% for i in range(1, totalPages) %}
        <li{% if i==setPage %} class="active" {% endif %}><a
                href="/users?page={{ i }}">{{ i }} {% if i==setPage %}<span class="sr-only">(current)</span>{% endif %}</a></li>
        {% endfor %}
    </ul>
</div>
{% include "global/footer.tpl.php" %}