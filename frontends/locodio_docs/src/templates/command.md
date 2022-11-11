# Command

`model` is the root variable of the data that is parsed with a Command template.

```
{{ model.name }}
{{ model.namespace }}
```

* `model.mapping` is the list with mapped properties (name, type)
* `model.domainModel` is the related model
* `model.project` is the related project

## Example data

```json
{
  "id": 9,
  "uuid": "f896aeb0-06d2-4726-ae3c-44daf90cf419",
  "name": "AddBook",
  "namespace": "App\\Project\\Application\\Command\\AddBook",
  "mapping": [
    {
      "name": "title",
      "type": "string"
    },
    {
      "name": "isbn",
      "type": "integer"
    },
    {
      "name": "coverType",
      "type": "enum"
    },
    {
      "name": "publishDate",
      "type": "date"
    },
    {
      "name": "authorId",
      "type": "integer"
    }
  ],
  "domainModel": {
    "id": 22,
    "uuid": "da0cb5f1-9444-4823-bf10-1cae29ae59d0",
    "sequence": 0,
    "name": "Book",
    "namespace": "App\\Project\\Domain\\Model",
    "repository": "App\\Project\\Infrastructure\\Database\\BookRepository",
    "fields": [...],
    "relations": [...]
  },
  "project": {
    "id": 10,
    "uuid": "a31ae95a-0dbc-4db9-9306-8bb69133a370",
    "code": "MFP",
    "name": "My First Project",
    "color": "#e61050",
    "domainLayer": "App\\Project\\Domain",
    "applicationLayer": "App\\Project\\Application",
    "infrastructureLayer": "App\\Project\\Infrastructure"
  }
}
```