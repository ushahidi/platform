---
description: Details for all tables in the Platform database
---

# Table details

### apikeys

| Field | Type | Null | Key | Default | Extra |
| :--- | :--- | :--- | :--- | :--- | :--- |
| id | int\(11\) | NO | PRI | NULL | auto\_increment |
| api\_key  | text | NO |  | NULL |  |
| client\_id | text | YES |  | NULL |  |
| client\_secret | text | YES |  | NULL |  |
| created | int\(11\) | NO |  | 0 |  |
| updated | int\(11\) | YES |  | NULL |  |



### config

| Field | Type | Null | Key | Default | Extra |
| :--- | :--- | :--- | :--- | :--- | :--- |
| id | int\(11\) | NO | PRI | NULL | auto\_increment |
| group\_name | varchar\(50\) | NO | MUL | NULL |  |
| config\_key | varchar\(50\) | NO |  | NULL |  |
| config\_value | text  | YES |  | NULL |  |
| updated |  timestamp | NO | MUL | CURRENT\_TIMESTAMP | on update CURRENT\_TIMESTAMP |

### contacts

| Field | Type | Null | Key | Default | Extra |
| :--- | :--- | :--- | :--- | :--- | :--- |
| id | int\(11\)      | NO | PRI | NULL | auto\_increment |
| user\_id | int\(11\) | YES | MUL | NULL |  |
| data\_source | varchar\(150\) | YES | MUL | NULL |  |
| type | varchar\(20\) | YES |  | NULL |  |
| contact | varchar\(255\) | NO |  | NULL |  |
| created | int\(11\) | NO | MUL | 0 |  |
| updated | int\(11\) | YES | MUL | NULL |  |
| can\_notify | tinyint\(1\) | NO |  | 0 |  |



### country\_codes

| Field | Type | Null | Key | Default | Extra |
| :--- | :--- | :--- | :--- | :--- | :--- |
| id | int\(11\)      | NO | PRI | NULL | auto\_increment |
| country\_name | varchar\(255\) | NO |  | 0 |  |
| dial\_code | varchar\(255\) | NO |  | 0 |  |
| country\_code | varchar\(255\) | NO | MUL | 0 |  |



### csv

| Field | Type | Null | Key | Default | Extra |
| :--- | :--- | :--- | :--- | :--- | :--- |
| id | int\(11\)      | NO | PRI | NULL | auto\_increment |
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



### export\_batches

| Field | Type | Null | Key | Default | Extra |
| :--- | :--- | :--- | :--- | :--- | :--- |
| id | int\(11\)      | NO | PRI | NULL | auto\_increment |
| export\_job\_id | int\(11\) | NO | MUL | NULL |  |
| status | varchar\(255\) | NO |  | pending |  |
| batch\_number | int\(11\) | NO |  | 0 |  |
| filename | varchar\(255\) | YES |  |  |  |
| has\_headers | tinyint\(1\) | NO |  | 0 |  |
| rows | int\(11\) | NO |  | 0 |  |
| created | int\(11\) | NO |  | 0 |  |
| updated | int\(11\) | YES |  | 0 |  |



### export\_job

| Field | Type | Null | Key | Default | Extra |
| :--- | :--- | :--- | :--- | :--- | :--- |
| id | int\(11\)      | NO | PRI | NULL | auto\_increment |
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



### form\_attribute\_hxl\_attribute\_tag 

| Field | Type | Null | Key | Default | Extra |
| :--- | :--- | :--- | :--- | :--- | :--- |
| id | int\(11\)      | NO | PRI | NULL | auto\_increment |
| form\_attribute\_id | int\(11\)  | NO | MUL | 0 |  |
| hxl\_attribute\_id | int\(11\)  | YES | MUL | NULL |  |
| hxl\_tag\_id | int\(11\)  | NO | MUL | 0 |  |
| export\_job\_id | int\(11\)  | NO | MUL | 0 |  |

