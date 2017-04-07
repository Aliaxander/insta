{% include "global/head.tpl.php" %}
<div class="container"><a href="/addProxy" class="btn btn-success" role="button">Add</a></div>

<table class="table">
    <thead>
    <tr>
        <th>#</th>
        <th>proxy</th>
        <th>status</th>
    </tr>
    </thead>
    <tbody>
    {% for proxy in proxyes %}
    {% if proxy.status == 0 %}
    <tr class="success">
        {% else %}
    <tr class="danger">
        {% endif %}
        <td>{{ proxy.id }}</td>
        <td>{{ proxy.proxy }}</td>
        <td>{{ proxy.status }}</td>
    </tr>
    {% endfor %}
    </tbody>
</table>
{% include "global/footer.tpl.php" %}