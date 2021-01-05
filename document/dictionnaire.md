# Dictionnaire des données

## COFFEE

|    Champ   |   Type   | Création de la table |                 Specific feature              |           Description          |
|:----------:|:--------:|:--------------------:|:---------------------------------------------:|:------------------------------:|
| id         | INT      |                      | PRIMARY KEY, UNSIGNED, AUTO_INCREMENT, UNIQUE | L'identifiant de l'utilisateur |
| name       | VARCHAR  | STRING               | UNIQUE                                        | Nom du café                    |
| country    | VARCHAR  | STRING               |                                               | Pays d'origine du café         |
| price      | FLOAT    | FLOAT                | UNSIGNED                                      | Prix du café                   |
| slug       | VARCHAR  | STRING               |                                               | Nom affiché du café            |
| created_at | DATETIME | DATETIME             | DEFAULT   CURRENT_TIMESTAMP                   | Date de création               |
| updated_at | DATETIME | DATETIME             | DEFAULT   CURRENT_TIMESTAMP, NULL             | Date de mise à jour            |

## ROASTING

|    Champ   |   Type   | Création de la table |                 Specific feature              |            Description           |
|:----------:|:--------:|:--------------------:|:---------------------------------------------:|:--------------------------------:|
| id         | INT      |                      | PRIMARY KEY, UNSIGNED, AUTO_INCREMENT, UNIQUE | L'identifiant de la torréfaction |
| name       | VARCHAR  | STRING               | UNIQUE                                        | Nom de la torréfaction           |
| slug       | VARCHAR  | STRING               |                                               | Nom de la torréfaction affichée  |
| created_at | DATETIME | DATETIME             | DEFAULT CURRENT_TIMESTAMP                     | Date de création                 |
| updated_at | DATETIME | DATETIME             | DEFAULT CURRENT_TIMESTAMP, NULL               | Date de mise à jour              |

## GALLERY

|    Champ   |   Type   | Création de la table |                 Specific feature              |        Description       |
|:----------:|:--------:|:--------------------:|:---------------------------------------------:|:------------------------:|
| id         | INT      |                      | PRIMARY KEY, UNSIGNED, AUTO_INCREMENT, UNIQUE | L'identifiant de l'image |
| name       | VARCHAR  | STRING               |                                               | Nom de l'image           |
| slug       | VARCHAR  | STRING               |                                               | Nom affiché              |
| created_at | DATETIME | DATETIME             | DEFAULT CURRENT_TIMESTAMP                     | Date de création         |
| updated_at | DATETIME | DATETIME             | DEFAULT CURRENT_TIMESTAMP, NULL               | Date de mise à jour      |

## USER

|    Champ   |   Type   | Création de la table |                 Specific feature              |           Description          |
|:----------:|:--------:|:--------------------:|:---------------------------------------------:|:------------------------------:|
| id         | INT      |                      | PRIMARY KEY, UNSIGNED, AUTO_INCREMENT, UNIQUE | L'identifiant de l'utilisateur |
| email      | VARCHAR  | STRING               | UNIQUE                                        | Email de l'utilisateur         |
| roles      | LONGTEXT | STRING               | DC2Type: json                                 | Rôles de l'utilisateur         |
| password   | VARCHAR  | FLOAT                |                                               | Mot de passe de l'utilisateur  |
| firstname  | VARCHAR  | STRING               |                                               | Prénom de l'utilisateur        |
| lasttname  | VARCHAR  | STRING               |                                               | Nom de l'utilisateur           |
| status     | VARCHAR  | STRING               |                                               | Status de l'utilisateur        |
| created_at | DATETIME | DATETIME             | DEFAULT   CURRENT_TIMESTAMP                   | Date de création               |
| updated_at | DATETIME | DATETIME             | DEFAULT   CURRENT_TIMESTAMP, NULL             | Date de mise à jour            |