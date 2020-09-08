---
description: Details for all tables in the Platform database
---

# Database \| Table details

## apikeys

| Field | Type | Null | Key | Default | Extra |
| :--- | :--- | :--- | :--- | :--- | :--- |
| id | int\(11\) | NO | PRI | NULL | auto\_increment |
| api\_key | text | NO |  | NULL |  |
| client\_id | text | YES |  | NULL |  |
| client\_secret | text | YES |  | NULL |  |
| created | int\(11\) | NO |  | 0 |  |
| updated | int\(11\) | YES |  | NULL |  |

## config

| Field | Type | Null | Key | Default | Extra |
| :--- | :--- | :--- | :--- | :--- | :--- |
| id | int\(11\) | NO | PRI | NULL | auto\_increment |
| group\_name | varchar\(50\) | NO | MUL | NULL |  |
| config\_key | varchar\(50\) | NO |  | NULL |  |
| config\_value | text | YES |  | NULL |  |
| updated | timestamp | NO | MUL | CURRENT\_TIMESTAMP | on update CURRENT\_TIMESTAMP |

## contacts

| Field | Type | Null | Key | Default | Extra |
| :--- | :--- | :--- | :--- | :--- | :--- |
| id | int\(11\) | NO | PRI | NULL | auto\_increment |
| user\_id | int\(11\) | YES | MUL | NULL |  |
| data\_source | varchar\(150\) | YES | MUL | NULL |  |
| type | varchar\(20\) | YES |  | NULL |  |
| contact | varchar\(255\) | NO |  | NULL |  |
| created | int\(11\) | NO | MUL | 0 |  |
| updated | int\(11\) | YES | MUL | NULL |  |
| can\_notify | tinyint\(1\) | NO |  | 0 |  |

## country\_codes

| Field | Type | Null | Key | Default | Extra |
| :--- | :--- | :--- | :--- | :--- | :--- |
| id | int\(11\) | NO | PRI | NULL | auto\_increment |
| country\_name | varchar\(255\) | NO |  | 0 |  |
| dial\_code | varchar\(255\) | NO |  | 0 |  |
| country\_code | varchar\(255\) | NO | MUL | 0 |  |

## csv

| Field | Type | Null | Key | Default | Extra |
| :--- | :--- | :--- | :--- | :--- | :--- |
| id | int\(11\) | NO | PRI | NULL | auto\_increment |
| columns | text | NO |  | NULL |  |
| maps\_to | text | YES |  | NULL |  |
| fixed | text | YES |  | NULL |  |
| filename | varchar\(255\) | NO |  | NULL |  |
| size | int\(11\) | NO |  | 0 |  |
| mime | varchar\(50\) | NO |  |  |  |
| created | int\(11\) | NO |  | 0 |  |
| updated | int\(11\) | YES |  | NULL |  |
| status | varchar\(255\) | YES |  | NULL |  |
| errors | varchar\(255\) | YES |  | NULL |  |
| processed | varchar\(255\) | YES |  | NULL |  |
| collection\_id | int\(11\) | YES |  | NULL |  |

## export\_batches

| Field | Type | Null | Key | Default | Extra |
| :--- | :--- | :--- | :--- | :--- | :--- |
| id | int\(11\) | NO | PRI | NULL | auto\_increment |
| export\_job\_id | int\(11\) | NO | MUL | NULL |  |
| status | varchar\(255\) | NO |  | pending |  |
| batch\_number | int\(11\) | NO |  | 0 |  |
| filename | varchar\(255\) | YES |  |  |  |
| has\_headers | tinyint\(1\) | NO |  | 0 |  |
| rows | int\(11\) | NO |  | 0 |  |
| created | int\(11\) | NO |  | 0 |  |
| updated | int\(11\) | YES |  | 0 |  |

## export\_job

| Field | Type | Null | Key | Default | Extra |
| :--- | :--- | :--- | :--- | :--- | :--- |
| id | int\(11\) | NO | PRI | NULL | auto\_increment |
| user\_id | int\(11\) | NO | MUL | NULL |  |
| entity\_type | varchar\(255\) | NO |  | NULL |  |
| fields | mediumtext | YES |  | NULL |  |
| filters | mediumtext | YES |  | NULL |  |
| status | varchar\(255\) | YES |  | NULL |  |
| url | text | YES |  | NULL |  |
| created | int\(11\) | NO |  | 0 |  |
| updated | int\(11\) | NO |  | 0 |  |
| url\_expiration | varchar\(12\) | YES |  | 0 |  |
| status\_details | varchar\(255\) | YES |  | 0 |  |
| header\_row | mediumtext | YES |  | NULL |  |
| hxl\_meta\_data\_id | int\(11\) | YES | UNI | NULL |  |
| include\_hxl | tinyint\(1\) | NO |  | 0 |  |
| send\_to\_browser | tinyint\(1\) | NO |  | 0 |  |
| send\_to\_hdx | tinyint\(1\) | NO |  | 0 |  |
| hxl\_heading\_row | mediumtext | YES |  | NULL |  |
| total\_rows | int\(11\) | YES |  | NULL |  |
| total\_batches | int\(11\) | YES |  | NULL |  |

## form\_attribute\_hxl\_attribute\_tag

| Field | Type | Null | Key | Default | Extra |
| :--- | :--- | :--- | :--- | :--- | :--- |
| id | int\(11\) | NO | PRI | NULL | auto\_increment |
| form\_attribute\_id | int\(11\) | NO | MUL | 0 |  |
| hxl\_attribute\_id | int\(11\) | YES | MUL | NULL |  |
| hxl\_tag\_id | int\(11\) | NO | MUL | 0 |  |
| export\_job\_id | int\(11\) | NO | MUL | 0 |  |

## form\_attribute

| Field | Type | Null | Key | Default | Extra |
| :--- | :--- | :--- | :--- | :--- | :--- |
|  |  |  |  |  |  |
| id | int\(11\) | NO | PRI | NULL | auto\_increment |
| key | varchar\(150\) | NO | UNI | NULL |  |
| label | varchar\(150\) | NO |  | NULL |  |
| instructions | text | YES |  | NULL |  |
| input | varchar\(30\) | NO |  | text |  |
| type | varchar\(30\) | NO |  | varchar |  |
| required | tinyint\(1\) | NO |  | 0 |  |
| default | varchar\(150\) | YES |  | NULL |  |
| priority | int\(11\) | NO | MUL | 99 |  |
| options | text | YES |  | NULL |  |
| cardinality | int\(11\) | NO |  | 1 |  |
| config | text | YES |  | NULL |  |
| form\_stage\_id | omt\(11\) | YES | MUL | NULL |  |
| response\_private | tinyint\(1\) | NO |  | 0 |  |
| description | varchar\(255\) | YES |  | NULL |  |

## form\_roles

| Field | Type | Null | Key | Default | Extra |
| :--- | :--- | :--- | :--- | :--- | :--- |
| id | int\(11\) | NO | PRI | NULL | auto\_increment |
| form\_id | int\(11\) | NO | MUL | NULL |  |
| role\_id | int\(11\) | NO | MUL | NULL |  |

## form\_stages

| Field | Type | Null | Key | Default | Extra |
| :--- | :--- | :--- | :--- | :--- | :--- |
| id | int\(11\) | NO | PRI | NULL | auto\_increment |
| form\_id | int\(11\) | NO | MUL | NULL |  |
| label | varchar\(150\) | NO |  | NULL |  |
| priority | int\(11\) | NO |  | 99 |  |
| icon | varchar\(100\) | YES |  | NULL |  |
| required | tinyint\(1\) | NO |  | 0 |  |
| type | varchar\(255\) | NO |  | task |  |
| description | varchar\(255\) | YES |  | NULL |  |
| show\_when\_published | tinyint\(1\) | NO |  | 1 |  |
| task\_is\_internal\_only | tinyint\(1\) | NO |  | 1 |  |

## form\_stages\_posts

| Field | Type | Null | Key | Default | Extra |
| :--- | :--- | :--- | :--- | :--- | :--- |
| form\_stage\_id | int\(11\) | NO | PRI | NULL |  |
| post\_id | int\(11\) | NO | PRI | NULL |  |
| completed | tinyint\(1\) | NO |  | 0 |  |

## forms

| Field | Type | Null | Key | Default | Extra |
| :--- | :--- | :--- | :--- | :--- | :--- |
| id | int\(11\) | NO | PRI | NULL | auto\_increment |
| parent\_id | int\(11\) | YES | MUL | NULL |  |
| name | varchar\(255\) | NO |  | NULL |  |
| description | text | YES |  | NULL |  |
| type | varchar\(30\) | NO |  | report |  |
| created | int\(11\) | NO | MUL | 0 |  |
| updated | int\(11\) | YES | MUL | NULL |  |
| disabled | tinyint\(1\) | NO |  | 0 |  |
| require\_approval | tinyint\(1\) | NO |  | 1 |  |
| everyone\_can\_create | tinyint\(1\) | NO |  | 1 |  |
| color | varchar\(6\) | YES |  | NULL |  |
| hide\_author | tinyint\(1\) | NO |  | 0 |  |
| hide\_time | tinyint\(1\) | NO |  | 0 |  |
| hide\_location | tinyint\(1\) | NO |  | 0 |  |
| targeted\_survey | tinyint\(1\) | NO |  | 0 |  |

## hxl\_attribute\_type\_tag

| Field | Type | Null | Key | Default | Extra |
| :--- | :--- | :--- | :--- | :--- | :--- |
| id | int\(11\) | NO | PRI | NULL | auto\_increment |
| form\_attribute\_type | varchar\(255\) | NO |  | 0 |  |
| hxl\_tag\_id | int\(11\) | NO | MUL | 0 |  |

## hxl\_attributes

| Field | Type | Null | Key | Default | Extra |
| :--- | :--- | :--- | :--- | :--- | :--- |
| id | int\(11\) | NO | PRI | NULL | auto\_increment |
| attribute | varchar\(255\) | NO | UNI | 0 |  |
| description | varchar\(255\) | NO |  | 0 |  |

## hxl\_license

| Field | Type | Null | Key | Default | Extra |
| :--- | :--- | :--- | :--- | :--- | :--- |
| id | int\(11\) | NO | PRI | NULL | auto\_increment |
| name | varchar\(255\) | NO | MUL | 0 |  |
| link | varchar\(255\) | NO |  | 0 |  |
| code | varchar\(255\) | NO | UNI | 0 |  |

## hxl\_meta\_data

| Field | Type | Null | Key | Default | Extra |
| :--- | :--- | :--- | :--- | :--- | :--- |
| id | int\(11\) | NO | PRI | NULL | auto\_increment |
| private | tinyint\(1\) | NO |  | 1 |  |
| dataset\_title | varchar\(255\) | NO | MUL | NULL |  |
| license\_id | int\(11\) | NO | MUL | NULL |  |
| user\_id | int\(11\) | NO | MUL | NULL |  |
| organisation\_id | varchar\(255\) | NO |  | NULL |  |
| source | varchar\(255\) | NO |  | NULL |  |
| created | int\(11\) | NO |  | 0 |  |
| updated | int\(11\) | YES |  | NULL |  |
| organisation\_name | varchar\(255\) | YES |  | NULL |  |

## hxl\_tag\_attributes

| Field | Type | Null | Key | Default | Extra |
| :--- | :--- | :--- | :--- | :--- | :--- |
| tag\_id | int\(11\) | NO | PRI | 0 |  |
| attribute\_id | int\(11\) | NO | PRI | 0 |  |

## hxl\_tags

| Field | Type | Null | Key | Default | Extra |
| :--- | :--- | :--- | :--- | :--- | :--- |
| id | int\(11\) | NO | PRI | NULL | auto\_increment |
| tag\_name | varchar\(255\) | NO | UNI | 0 |  |
| description | varchar\(255\) | NO |  | 0 |  |

## layers

| Field | Type | Null | Key | Default | Extra |
| :--- | :--- | :--- | :--- | :--- | :--- |
| id | int\(11\) | NO | PRI | NULL | auto\_increment |
| media\_id | int\(11\) | YES | MUL | NULL |  |
| name | varchar\(50\) | NO |  | NULL |  |
| type | varchar\(20\) | NO |  | geojson |  |
| data\_url | varchar\(255\) | YES |  | NULL |  |
| options | varchar\(255\) | YES |  | NULL |  |
| acte | int\(1\) | NO |  | 1 |  |
| invisible\_by\_default | int\(1\) | NO |  | 1 |  |
| created | int\(11\) | NO | MUL | 0 |  |
| updated | int\(11\) | NO | MUL | 0 |  |

## media

| Field | Type | Null | Key | Default | Extra |
| :--- | :--- | :--- | :--- | :--- | :--- |
| id | int\(11\) | NO | PRI | NULL | auto\_increment |
| user\_id | int\(11\) | YES | MUL | NULL |  |
| mime | varchar\(50\) | NO |  | NULL |  |
| caption | varchar\(255\) | NO |  |  |  |
| o\_filename | varchar\(255\) | NO |  | NULL |  |
| o\_size | int\(11\) | NO |  | NULL |  |
| o\_width | int\(11\) | YES |  | NULL |  |
| o\_height | int\(11\) | YES |  | NULL |  |
| created | int\(11\) | NO | MUL | 0 |  |
| updated | int\(11\) | YES | MUL | NULL |  |

## messages

| Field | Type | Null | Key | Default | Extra |
| :--- | :--- | :--- | :--- | :--- | :--- |
| id | int\(11\) | NO | PRI | NULL | auto\_increment |
| parent\_id | int\(11\) | YES | MUL | NULL |  |
| contact\_id | int\(11\) | YES | MUL | NULL |  |
| post\_id | int\(11\) | YES | MUL | NULL |  |
| user\_id | int\(11\) | YES | MUL | NULL |  |
| data\_source | varchar\(150\) | YES | MUL | NULL |  |
| data\_source\_message\_id | varchar\(255\) | YES |  | NULL |  |
| title | varchar\(255\) | YES |  | NULL |  |
| message | text | YES |  | NULL |  |
| datetime | datetime | YES |  | NULL |  |
| type | varchar\(20\) | YES | MUL | NULL |  |
| status | varchar\(20\) | NO | MUL | pending |  |
| direction | varchar\(20\) | NO | MUL | incoming |  |
| created | int\(11\) | NO | MUL | 0 |  |
| additional\_data | text | YES |  | NULL |  |
| notification\_post\_id | int\(11\) | YES | MUL | NULL |  |

## notification\_queue

| Field | Type | Null | Key | Default | Extra |
| :--- | :--- | :--- | :--- | :--- | :--- |
| id | int\(11\) | NO | PRI | NULL | auto\_increment |
| post\_id | int\(11\) | NO | MUL | NULL |  |
| set\_id | int\(11\) | NO | MUL | NULL |  |
| created | int\(11\)int\(11\) | NO |  | 0 |  |

