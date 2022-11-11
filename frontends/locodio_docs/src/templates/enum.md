# Enum

`enum` is the root variable of the data that is parsed with a Enum template.

```
{{ enum.name }}
{{ enum.namespace }}
```

* `enum.options` is the list with the options (code, value)
* `enum.domainModel` is the related model 
* `enum.project` is the related project

## Example data

```json
{
  "id": 19,
  "uuid": "03866e68-0cf6-4460-9e2b-3c6e58b0b667",
  "name": "CoverType",
  "namespace": "App\\Project\\Domain\\Model",
  "options": [
    {
      "id": 65,
      "uuid": "aa1ba48a-f123-493d-9f56-a969f6b104b2",
      "sequence": 0,
      "code": "HARD_COVER",
      "value": "hard_cover"
    },
    {
      "id": 66,
      "uuid": "dff70b52-28a5-4493-8862-93709701a0d9",
      "sequence": 0,
      "code": "SOFT_COVER",
      "value": "soft_cover"
    }
  ],
  "domainModel": {
    "id": 22,
    "uuid": "da0cb5f1-9444-4823-bf10-1cae29ae59d0",
    "sequence": 0,
    "name": "Book",
    "namespace": "App\\Project\\Domain\\Model",
    "repository": "App\\Project\\Infrastructure\\Database\\BookRepository"
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