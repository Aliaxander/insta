{% include "global/head.tpl.php" %}
<div class="table-responsive">
    <table class="table">
        <thead>
        <tr>
            <th>#</th>
            <th>userName</th>
            <th>firstName</th>
            <th>email</th>
            <th>password</th>
            <th>logIn</th>
            <th>gender</th>
            <th>proxy</th>
            <th>follows</th>
            <th>likes</th>
            <th>dateCreate</th>
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
            <td>{{ user.id }}</td>
            <td><a href="https://instagram.com/{{ user.userName }}" target="_blank">{{ user.userName }}</a></td>
            <td>{{ user.firstName }}</td>
            <td>{{ user.email }}</td>
            <td>{{ user.password }}</td>
            <td>{{ user.logIn }}</td>
            <td>{{ user.gender }}</td>
            <td>{{ user.proxy }}</td>
            <td>{{ user.follows }}</td>
            <td>{{ user.likes }}</td>
            <td>{{ user.dateCreate }}</td>
        </tr>
        {% endfor %}
        </tbody>
    </table>
</div>
{% include "global/footer.tpl.php" %}