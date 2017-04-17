{% include "global/head.tpl.php" %}
<div class="table-responsive">
    <table class="table">
        <thead>
        <tr>
            <th><a href="?orderBy=id&sort=desc">id</a></th>
            <th><a href="?orderBy=userName&sort=desc">userName</a></th>
            <th><a href="?orderBy=firstName&sort=desc">firstName</a></th>
            <th><a href="?orderBy=email&sort=desc">email</a></th>
            <th><a href="?orderBy=password&sort=desc">password</a></th>
            <th><a href="?orderBy=deviceId&sort=desc">deviceId</a></th>
            <th><a href="?orderBy=phoneId&sort=desc">phoneId</a></th>
            <th><a href="?orderBy=waterfall_id&sort=desc">waterfall_id</a></th>
            <th><a href="?orderBy=guid&sort=desc">guid</a></th>
            <th><a href="?orderBy=qeId&sort=desc">qeId</a></th>
            <th><a href="?orderBy=accountId&sort=desc">accountId</a></th>
            <th><a href="?orderBy=proxy&sort=desc">proxy</a></th>
            <th><a href="?orderBy=userAgent&sort=desc">userAgent</a></th>
            <th><a href="?orderBy=dateCreate&sort=desc">dateCreate</a></th>
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
            <td>{{ user.userName }}</td>
            <td>{{ user.firstName }}</td>
            <td>{{ user.email }}</td>
            <td>{{ user.password }}</td>
            <td>{{ user.deviceId }}</td>
            <td>{{ user.phoneId }}</td>
            <td>{{ user.waterfall_id }}</td>
            <td>{{ user.guid }}</td>
            <td>{{ user.qeId }}</td>
            <td>{{ user.accountId }}</td>
            <td>{{ user.proxy }}</td>
            <td>{{ user.userAgent }}</td>
            <td>{{ user.dateCreate }}</td>
        </tr>
        {% endfor %}
        </tbody>
    </table>
    <button class="btn btn-danger" onclick="del()">Delete</button>
    <ul class="pagination pull-right" style="margin: 0;">
        {% for i in range(1, totalPages) %}
        <li
            {% if i==setPage %} class="active" {% endif %}><a
                href="/users?page={{ i }}">{{ i }} {% if i==setPage %}<span class="sr-only">(current)</span>{% endif
                %}</a></li>
        {% endfor %}
    </ul>
</div>
{% include "global/footer.tpl.php" %}
