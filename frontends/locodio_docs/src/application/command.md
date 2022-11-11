# Command

This is a more _opinionated_ screen because it refers to a CQRS (see below) approach of
building applications. Off course not all software applications need a CQRS
approach. But locod.io is designed to support this pattern.

:::tip
More information about [CQRS and CRUD](https://www.martinfowler.com/bliki/CQRS.html).
By Martin Fowler.
:::

This screen is about making __Commands__ based on a model. A command is an
instruction to change some information of the application. A Command usually only
consists of basic properties (integers, strings, booleans,...)

Here you make a Command and decide what properties (based on a model) you want to map.
This is useful for mapping commands that adds and change model information. 

With the lightning icon you can prefill the namespace.

![Command](/command.png)

At the bottom of the page you can choose for which template you want to
generate code for. After choosing a template you can click on the generate button
to see the resulting code.

::: tip
[Command Query Responsibility Segregation](https://en.wikipedia.org/wiki/Command%E2%80%93query_separation)
(CQRS) is an architectural pattern for separating
reading data (a 'query') from writing to data (a 'command'). CQRS derives from
Command and Query Separation (CQS), coined by Greg Young.
:::