# Query 

This is a more _opinionated_ screen because it refers to a CQRS (see below) approach of 
building applications. Off course not all software applications need a CQRS 
approach. But locod.io is designed to support this pattern. Of course if your
application is CRUD based, you can only use the __model__ and the __enum__ functionalities.

:::tip
More information about [CQRS and CRUD](https://www.martinfowler.com/bliki/CQRS.html).
By Martin Fowler.
:::

This screen is about making __ReadModels__ (RM) based on a model.
First you choose the model on which the RM is based and then you choose
which fields you want to be mapped in the RM.
Also you can assign a namespace of the RM. 
With the lightning icon you can prefill this namespace. 

![Query](/query.png)

At the bottom of the page you can choose for which template you want to
generate code for. After choosing a template you can click on the generate button
to see the resulting code.

::: tip
[Command Query Responsibility Segregation](https://en.wikipedia.org/wiki/Command%E2%80%93query_separation)
(CQRS) is an architectural pattern for separating
reading data (a 'query') from writing to data (a 'command'). CQRS derives from
Command and Query Separation (CQS), coined by Greg Young.
:::