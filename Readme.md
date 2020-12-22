Ubi_student_api
==
***
Présentation
--
***
Ubi_student_api est la réponse à un test technique client. Il a été réalisé sans limite de temps pour une durée 
approximative de 4 jours ouvrés en démarrant de rien.

Énoncé fourni
--
***
**Créer une API de notation d'élèves en Symfony**

Un élève est caractérisé par :
- Un nom
- Un prénom
- Une date de naissance

Une note est caractérisée par :
- Une valeur : entre 0 et 20
- Une matière : Champ texte

L'API devra permettre de :
- Ajouter un élève
- Modifier les informations d'un élève (nom, prénom, date de naissance)
- Supprimer un élève
- Ajouter une note à un élève
- Récupérer la moyenne de toutes les notes d'un élève
- Récupérer la moyenne générale de la classe (moyenne de toutes les notes données)

Une attention particulière sera donnée aux respects des bonnes pratiques de code et de
construction des API. Aussi, veuillez nous fournir une documentation précise pour l'utilisation
de votre API.

Choix techniques
--
***

- Le projet est développé avec PHP 7.4 sur une base Symfony 5 selon la méthodologie 
  [TDD](https://en.wikipedia.org/wiki/Test-driven_development).
- Par simplicité j'ai utilisé le moteur de base de données 
  [SQLite](https://www.sqlite.org/index.html)

- Bien qu'incontournable sur ce type de projet j'ai choisi de ne pas utiliser [Api-platform](https://api-platform.com/)
  pour démontrer ma connaissance du framework.
- L'Api ne prend en charge le format Json (pour des formats multiples ou plus complexes [Api-platform](https://api-platform.com/)
  est vraiment plus simple).

Prérequis
--
***
- (Recommandé) Installez le client [Symfony](https://symfony.com/download) pour disposer facilement
  d'un serveur de test et d'un outil de contrôle des prérequis.
- Assurez-vous que votre poste remplisse les pré-requis techniques de la 
  [documentation Symfony](https://symfony.com/doc/5.2/setup.html#technical-requirements)
- Paramétrez un serveur de base de données ou installez [sqlite3](https://www.sqlite.org/download.html)
  
Installation
--
***
- Cloner le code source `git clone https://github.com/Lreus/ubi-student-api.git`
- Installer les ressources du projet: `composer install`
- Faites une copie du fichier .env.dist et nommez la .env
- Configurer votre base de données dans le fichier .env à l'aide des chaines préconfigurées
- Générez votre base de données: `./bin/console doctrine:migrations:migrate` 

Documentation API 
--
***
Un appel sur la [racine du serveur]((http://localhost::8000/)) ou l'url [api/doc](http://localhost::8000/api/doc) renverra
la documentation au format json.

Pour une interface plus accueillante, collez le contenu du fichier 
[public/doc.yaml](https://github.com/Lreus/ubi-student-api/blob/develop/public/doc.yaml) dans 
l'éditeur en ligne [swagger.io](https://editor.swagger.io/). 

Utilisation
--
***
- Démarrez votre serveur de test (si vous utilisez le client Symfony: `symfony local:server:start`)
- Utilisez [curl](https://curl.se/) ou un client Api comme [Postman](https://www.postman.com/) pour interroger l'api.

