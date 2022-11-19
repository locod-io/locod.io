# Model

`model` is the root variable of the data that is parsed with a Model template.

```
{{ model.name }}
{{ model.namespace }}
{{ model.repository }}
```

* `model.project` is the related project
* `model.attributes` is the list with attributes (properties)
* `model.associations` is the list of associations (relations)

## Types

### Attribute types

- integer
- float
- string
- text
- boolean
- date
- date_immutable
- date_time
- date_time_immutable
- array
- simple_array
- json
- uuid
- email
- enum

### Association types

- Many-To-One_Unidirectional
- One-To-One_Unidirectional
- One-To-One_Bidirectional
- One-To-One_Self-referencing
- One-To-Many_Bidirectional
- One-To-Many_Self-referencing
- Many-To-Many_Unidirectional
- Many-To-Many_Bidirectional
- Many-To-Many_Self-referencing

## Example data

```json
{
  "id": 13,
  "uuid": "f8ca3b13-6fd5-43c3-8a19-4fda619229aa",
  "sequence": 0,
  "name": "Book",
  "namespace": "App\\Project\\Domain\\Model",
  "repository": "App\\Project\\Infrastructure\\Database\\BookRepository",
  "project": {
    "id": 4,
    "uuid": "9b5b3ab0-7a22-414f-be16-deb23542bccf",
    "code": "BOOK",
    "name": "Example Book Project",
    "color": "#ff9100",
    "domainLayer": "App\\Project\\Domain",
    "applicationLayer": "App\\Project\\Application",
    "infrastructureLayer": "App\\Project\\Infrastructure"
  },
  "attributes": [
    {
      "id": 44,
      "uuid": "84a7404a-6a45-44f7-b3dc-69e39dc719ac",
      "sequence": 0,
      "name": "id",
      "length": 0,
      "type": "integer",
      "identifier": true,
      "required": false,
      "unique": true,
      "make": false,
      "change": false,
      "enum": null
    },
    {
      "id": 87,
      "uuid": "8fef6f36-7b08-4d77-ace7-d092f69a2510",
      "sequence": 1,
      "name": "isbn",
      "length": 0,
      "type": "integer",
      "identifier": false,
      "required": false,
      "unique": true,
      "make": true,
      "change": false,
      "enum": null
    },
    {
      "id": 49,
      "uuid": "8f3686e7-c527-4a13-8340-ca71b35e5348",
      "sequence": 2,
      "name": "title",
      "length": 191,
      "type": "string",
      "identifier": false,
      "required": false,
      "unique": false,
      "make": true,
      "change": true,
      "enum": null
    },
    {
      "id": 48,
      "uuid": "026b9926-7866-478e-b4a2-59b7783b7654",
      "sequence": 3,
      "name": "coverType",
      "length": 0,
      "type": "enum",
      "identifier": false,
      "required": false,
      "unique": false,
      "make": true,
      "change": true,
      "enum": {
        "id": 7,
        "uuid": "3a795472-cbba-4424-8090-699cffea02b3",
        "name": "CoverType",
        "namespace": "App\\Project\\Domain\\Model",
        "options": [
          {
            "id": 36,
            "uuid": "9b167f91-8409-4ee0-bd28-f1e4b50ff6be",
            "sequence": 0,
            "code": "HARD_COVER",
            "value": "hard_cover"
          },
          {
            "id": 35,
            "uuid": "cffaad9e-6e92-4260-830f-8f93e47bf1d6",
            "sequence": 1,
            "code": "SOFT_COVER",
            "value": "soft_cover"
          }
        ],
        "domainModel": {
          "id": 13,
          "uuid": "f8ca3b13-6fd5-43c3-8a19-4fda619229aa",
          "sequence": 0,
          "name": "Book",
          "namespace": "App\\Project\\Domain\\Model",
          "repository": "App\\Project\\Infrastructure\\Database\\BookRepository"
        }
      }
    },
    {
      "id": 97,
      "uuid": "dca10201-e364-4df1-b243-381c7404b6b6",
      "sequence": 4,
      "name": "publishDate",
      "length": 0,
      "type": "date",
      "identifier": false,
      "required": false,
      "unique": false,
      "make": true,
      "change": true,
      "enum": null
    }
  ],
  "associations": [
    {
      "id": 26,
      "uuid": "9bec92ec-17af-4585-9214-07925abd5287",
      "sequence": 0,
      "type": "Many-To-One_Unidirectional",
      "mappedBy": "author",
      "inversedBy": "books",
      "fetch": "EXTRA_LAZY",
      "orderBy": "id",
      "orderDirection": "ASC",
      "targetDomainModel": {
        "id": 14,
        "uuid": "aa9d2023-8086-4cfc-b33d-9399b28d82cd",
        "sequence": 1,
        "name": "Author",
        "namespace": "App\\Project\\Domain\\Model",
        "repository": "App\\Project\\Infrastructure\\Database\\AuthorRepository"
      },
      "make": true,
      "change": true,
      "required": true
    }
  ]
}
```