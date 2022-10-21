# Projet de fin de formation : Livres O'Trésor - version back (API)

Gestionnaire de bibliothèque pour enfant doublé d'une motivation à la lecture.</br></br>
Débuté en équipe de 5 développeurs du 9 aout au 9 septembre 2022: </br></br>
• Marie Lou Prince-Levasseur (back)</br>
• Tiphany Quemeneur (back)</br>
• Aswan Joseph-mathieu (back)</br>
• Cédric Cochard (front)</br>
• Maxime Kerkheide (front)


## Gestion des difficultés</br>
 • Double voie de connexion (enfant ou parent)</br>
 • Barre de progression</br>
 • Sécurisation API </br>
 • Gestion des CORS


### Techniques et outils utilisés: 

Front en ReactJS
Back en Symfony

#### Général
    • Sensio/framework-extra-bundle : permet de créer des configuration pour les controllers directement en annotation. Il permet de récupérer entre autres :
        ◦ @Route and @Method : pour créer les routes
        ◦ @ParamConverter : récupérer les paramètres variables dans une route (exemple le numéro ID dans la route)
        ◦ @IsGranted : permet de donner une autorisation d’accès pour une route en fonction du rôle de l’utilisateur.
    • Symfony/serializer : Récupère/transforme des objets dans un encodage spécifique (XML, JSON, YAML, …)
    • Symfony/validator : outil permettant de valider les contraintes d’un objet en vérifier les règles à valider
    • Fakerphp/faker : permet la création de fausses données pour simuler du contenu avant mise en production
#### Base de données
    • MySQL : Base de données relationnelles permettant la collecte et l’utilisation des données nécessaires au fonctionnement du site. Utilisation de l’interface Adminer.
    • Doctrine (ORM :Object Relational Mapping) : permet l’interaction avec la base de données (requêtes, modifications, suppressions, ajouts)
#### Sécurité
    • JWT Token (lexik/jwt-authentication-bundle) : permet d’octroyer le jeton d’identification unique lors de la réussite de la connexion qui sera nécessaire pour sécuriser l’accès aux différentes pages du site.
    • Nelmio/cors-bundle : permet la gestion et l’autorisation des CORS pour que le front puisse requêter l’API back.
    • Symfony/security-bundle : permet la configuration du pare-feu, du contrôle des accès par rôle utilisateur, de l’authentification de la connexion. 
