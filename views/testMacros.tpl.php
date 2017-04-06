{% include "global/head.tpl.php" %}

<div class="container">
    <div class="rows">
        <form>
            <div class="form-group">
                <label for="inputEmail3" class="control-label">Description</label>
                <textarea name="macros" class="form-control" rows="4"
                          placeholder="{Добрый день|Доброе утро}! Какая {на улице|за {окном|бортом}} {прекрасная|очаровательная|великолепная} погода!"></textarea>
            </div>
            <div class="form-group">
                <div>
                    <button type="submit" class="btn btn-success">Test</button>
                </div>
            </div>
        </form>
        <ul>
            {% for macros in data %}
            <li>{{ macros }}</li>
            {% endfor %}
        </ul>
    </div>
</div>
{% include "global/footer.tpl.php" %}