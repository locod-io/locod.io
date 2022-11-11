# Creating Templates

The templating engine that locod.io is using is 
[Twig](https://twig.symfony.com/doc/3.x/templates.html).  

## The basics

### Rendering variables

Rendering a variable in a template is done with following syntax.  

```
{{ model.name }}
```

Filters can be set on variables with the filter name after the pipe.
A list of filters can be found on the [reference page of the 
Twig documentation](https://twig.symfony.com/doc/3.x/templates.html#filters) 
and in the [tips and tricks](tips.md#common-variable-name-filters).

```
{{ model.name | upper }}
```

### Loop over lists

You can loop over lists or array's with following syntax. 

```
{% for field in model.fields %}
    ...
{% endfor %}
```


### Conditional rendering 

The following comparison operators are supported in any expression: ==, !=, <, >, >=, and <=.

```
{% if field.type == 'integer' %}
    ...
{% elseif field.type == 'string' %}
    ...
{% endif %}
```

#### Logic 

You can combine multiple expressions with the following operators:
* `and`: Returns true if the left and the right operands are both true.
* `or`: Returns true if the left or the right operand is true.
* `not`: Negates a statement.

Note: Operators are case-sensitive.

```
{% if field.type == 'integer' and field.identifier == true %}
    ...
{% endif %}
```

## Generating code

When generating code, the template is parsed with the information
provided in your model (model, enum, query and command).
That results in source code that is ready to copied into your project.

### Structure of the locod.io model

![Overview locodio model](/locodio_model.png)

### Data examples

Example structures filled with demo data, naming of the variables 
and lists can be found here:
* [Model](model.md#example-data) (or DomainModel, Entity)
* [Enum](enum.md#example-data)
* [Query](query.md#example-data) (ReadModels)
* [Command](command.md#example-data)

## Full reference

The full reference for the Twig templating language, 
[is available here](https://twig.symfony.com/doc/3.x/templates.html#twig-for-template-designers). 
