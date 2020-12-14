# Dictionnaire des données

## COFFEE

|    Champ   |   Type   | Création de la table |                 Specific feature              |           Description          |
|:----------:|:--------:|:--------------------:|:---------------------------------------------:|:------------------------------:|
| id         | INT      |                      | PRIMARY KEY, UNSIGNED, AUTO_INCREMENT, UNIQUE | L'identifiant de l'utilisateur |
| name       | VARCHAR  | STRING               | UNIQUE                                        | Nom du café                    |
| country    | VARCHAR  | STRING               |                                               | Pays d'origine du café         |
| price      | INT      | INT                  | UNSIGNED                                      | Prix du café                   |
| created_at | DATETIME | DATETIME             | DEFAULT   CURRENT_TIMESTAMP                   | Date de création du café       |
| updated_at | DATETIME | DATETIME             | DEFAULT   CURRENT_TIMESTAMP, NULL             | Date de mise à jour du café    |

## ROASTING

|    Champ   |   Type   | Création de la table |                 Specific feature              |            Description           |
|:----------:|:--------:|:--------------------:|:---------------------------------------------:|:--------------------------------:|
| id         | INT      |                      | PRIMARY KEY, UNSIGNED, AUTO_INCREMENT, UNIQUE | L'identifiant de la torréfaction |
| name       | VARCHAR  | STRING               |                                               | Torréfaction                     |
| created_at | DATETIME | DATETIME             | DEFAULT CURRENT_TIMESTAMP                     | Date de création du café         |
| updated_at | DATETIME | DATETIME             | DEFAULT CURRENT_TIMESTAMP, NULL               | Date de mise à jour du café      |

## GALLERY

|    Champ   |   Type   | Création de la table |                 Specific feature              |         Description         |
|:----------:|:--------:|:--------------------:|:---------------------------------------------:|:---------------------------:|
| id         | INT      |                      | PRIMARY KEY, UNSIGNED, AUTO_INCREMENT, UNIQUE | L'identifiant de l'image    |
| name       | VARCHAR  | STRING               |                                               | Nom de l'image              |
| slug       | VARCHAR  | STRING               |                                               | Nom affiché au client       |
| created_at | DATETIME | DATETIME             | DEFAULT CURRENT_TIMESTAMP                     | Date de création du café    |
| updated_at | DATETIME | DATETIME             | DEFAULT CURRENT_TIMESTAMP, NULL               | Date de mise à jour du café |