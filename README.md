# Projet 6 : SnowTricks

### Contexte
Jimmy Sweat est un entrepreneur ambitieux passioné de snowboard. Son objectif est la création d'un site collaboratif
pour faire connaître ce sport auprès du grand public et aider à l'apprentissage des figures (tricks).

Il souhaite capitaliser sur du contenu apporté par les internautes afin de développer un contenu riche et suscitant l'intérêt des utilisateurs.
Par la suite, Jimmy souhaite développer un business de mise en relation avec les marques de snowvoard grâce au trafic que le contenu aura généré.

Pour ce projet, nous allons nous concentrer sur la création technique du site pour Jimmy.

### Description du besoin
Fonctionnalités demandées :
    
    - Annuaire des figures de snowboard
    - La gestion des figures (création, modification, consultation)
    - Un espace de discussion commun à toutes les figures
    
Pages demandées :

    - Page d'accueil avec la liste des figures accessibles à tous les utilisateurs.
    L'utilisateur a la possibilité de cliquer sur le nom d'une figure pour accéder 
    à la page de détail de cette figure.
    Si l'utilisateur est connecté, il pourra cliquer sur :
        - une petite icone en forme de stylo situé juste à côté du nom qui redirigera l'utilisateur
        vers un formulaire de modification de figure;
        - une corbeille située juste à côté du nom pour supprimer la figure.
        
    - Page de création d'une nouvelle figure (accessible uniquement aux utilisateurs connecté) 
    comportant un formulaire avec les champs suivants :
        - nom
        - description
        - groupe de la figure
        - une ou plusieurs illustrations
        - une ou plusieurs videos
        
    A la soumission du formulaire, il faut :
        - cette figure n'existe pas déjà en BDD (contrainte d'unicité sur le nom)
        - il soit redirigé sur la page du formulaire en cas d'erreur, en précisant le type d'erreur
        - il soit redirigé sur la page listant des figures avec un message flash donnant une indication
         concernant le bon déroulement de l'enregistrement en BDD en cas de succés.
         
    Pour les videos, l'utilisateur pourra coller une balise embed provenant de la plateforme de son choix.
    
    - Page de modification d'une figure
    Les besoins sont les mêmes que pour la création. La seule différence est qu'il faut que les champs soient pré remplis
    au moment ou l'utilisateur arrive sur cette page.
    
    - Page de présentation d'une figure (contenant l'espace de discussion commun autour d'une figure)
    Les informations suivantes doivent figurer sur cette page :
        - nom de la figure
        - sa description
        - le groupe de la figure
        - la ou les photos rattachées à la figure
        - la ou les videos rattachées à la figure
        - l'espace de discussion
        
    - Espace de discussion commun autour d'une figure
    Les utilisateurs qui ne sont pas authentifiés peuvent consulter les discussions de toutes les figures
    mais ils ne peuvent pas poster de message.
    
    Pour chaque message, il sera affichés les informations suivantes :
        - le nom complet de l'auteur du message
        - la photo de l'auteur du message
        - la date de création du message
        - le contenu du message
        
    Le liste est ordonnée du plus récent au plus ancien et être paginés (10 par pages)
    
    Si l'utilisateur est authentifié, il peut voir un formulaire au dessus de la liste avec un champs "message" qui est obligatoire.
    L'utilisateur peut poster autant de message qu'il le souhaite.
    
Nota bene :

    - Les URL doivent permettrent une compréhension rapide de ce que la page représente et que le référencement naturel soit facilité
    - L'utilisation de bundles tiers est interdite sauf pour les données initiales.
    - Le design est laissé libre mais suivre les wireframes, et être responsive.
    
### Organisation
    1. Setup architecture and environnement (linter, diagrams, bdd, connect to bdd)
    2. Front => Design Home Page, Back => Dynamic tricks list data
    
