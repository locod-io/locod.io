# Tips and tricks

### Start or end of a list

Sometimes you want to know when you are at the beginning or at the end
of a list. With following snippets you can use some special loop variables. 

```
{% for attribute in model.attributes %}
    {% if (loop.first %} ... {% endif %}
    ...
{% endfor %}
```

```
{% for attribute in model.attributes %}
    ...
    {% if (loop.last %} ... {% endif %}
{% endfor %}
```

:::tip
Inside of a for loop block you can access some special variables:

Variable | 	Description
--- | ---
`loop.index`|The current iteration of the loop. (1 indexed)
`loop.index0`|The current iteration of the loop. (0 indexed)
`loop.revindex`|The number of iterations from the end of the loop (1 indexed)
`loop.revindex0`|The number of iterations from the end of the loop (0 indexed)
`loop.first`|True if first iteration
`loop.last`|True if last iteration
`loop.length`|The number of items in the sequence
`loop.parent`|The parent context
:::

## Common variable name filters

In some cases the name of your variable (field, relation, enum,...) 
should be represented in an different way than you have defined in the model.

### Camel case

```
{{ model.name | u.camel }}
```

`some_variable_name` becomes `someVariableName`

:::tip
[https://en.wikipedia.org/wiki/Camel_case](https://en.wikipedia.org/wiki/Camel_case)
:::

### Pascal case or Bumpy case

```
{{ model.name | u.camel.title }}
```

`some_variable_name` becomes `SomeVariableName`

`someVariableName` becomes `SomeVariableName`, eg. handy for getters and setters

If you want for example a getter function like this:

```php
function getSomeAttributeName() {
    return $this->attributeName;
}
```
Can be written in a template with following snippet:

```
function get{{attribute.name | u.camel.title}}() {
    return $this->{{attribute.name}}
} 
```

### Snake case

Eg. snake case names are commonly used for naming fields in databases.

```
{{model.name | u.snake}}
```

`someVariableName` becomes `some_variable_name`

:::tip
[https://en.wikipedia.org/wiki/Snake_case](https://en.wikipedia.org/wiki/Snake_case)
:::