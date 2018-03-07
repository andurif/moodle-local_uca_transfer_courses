Moodle UCA - Transfert de cours entre deux plateformes
==================================
Projet ayant pour but de transférer des cours d'une plateforme Moodle à une autre "automatiquement". Les utilisateurs n'ayant qu'à passer commande d'un transfert.

Pré-requis
------------
- Moodle en version 3.3 (build 2017051500) ou plus récente.<br/>
-> Tests effectués en transférant des cours d'une version 3.1 vers une version 3.2 et 3.3.
- Thème qui supporte bootstrap.
- Plugin JS Jstree => https://github.com/vakata/jstree (joint dans le dossier jstree/).
- Guzzle (embarqué de base dans les vendors de Moodle).

Installation
------------
1. Installation du plugin

- Avec git:
> git clone https://github.com/andurif/moodle-local_uca_transfer_courses.git local/uca_transfer_courses

- En téléchargement:
> Télécharger le zip depuis <a href="https://github.com/andurif/moodle-local_uca_mycourses/archive/master.zip">https://github.com/andurif/moodle-local_uca_mycourses/archive/master.zip</a>, dézipper l'archive dans le dossier local/ et renommer le si besoin le dossier en "uca_transfer_courses".
  
2. Déplacer le sous-dossier export/ vers le projet de la plateforme d'où vont être exporté les cours (pas d'emplacements spécifiques mais il faudra spécifier l'url des ces scripts dans l'administration de votre nouvelle plateforme). Des chanegements au niveau de l'intégration du fichier config.php seront peut-être à apporter en fonction de là où vous déposerez le dossier (-> ligne require('../../../config.php'); au début des fichiers import.php et export.php).<br/>

3. Aller sur la page de Notifications pour terminer l'installation du plugin.

4. Une fois l'installation terminée, plusieurs options d'administration sont à renseigner:

> Site administration -> Plugins -> Plugins locaux -> Transfert de cours -> course_transfer_url

Option permettant de renseigner l'url à laquelle retrouver le fichier json listant les cours que l'utilisateur peut transférer (url du script export.php du dossier export/ du plugin installé sur votre ancien moodle).

> Site administration -> Plugins -> Plugins locaux -> Transfert de cours -> course_export_url

Option permettant de renseigner l'url à laquelle le script permettant l'export d'un cours (url du script import.php du dossier export/ du plugin installé sur votre ancien moodle).

 > Site administration -> Plugins -> Plugins locaux -> Transfert de cours -> transfer_archives_folder
 
Option permettant de renseigner le chemin du dossier où sont déposées les archives des cours à transférer (logiquement dans le dossier moodledata de votre ancien moodle). Attention, ce dossier doit être accessible depuis le serveur de la nouvelle plateforme. Attention, ce chemin de dossier doit correspondre avec le dossier $folder défini au début du fichier /export/import.php (ligne 18).

5. Pour l'affichage de l'arbre des catégories nous avons été obligé de modifier quelques éléments du code du core de Moodle dans le fichier course/renderer.php. (Point à améliorer car les modifications peuvent être à refaire en cas de mise à jour de Moodle).<br/>
(Risque d'avoir ce type de message d'erreur sinon "Erreur de programmation détectée. Ceci doit être corrigé par un programmeur : Unknown method called against theme_boost\output\core\course_renderer :: coursecat_tree").
  > environ l.1580: faire passer la fonction coursecat_tree() en public
  
  > environ l. 1767 et 1771: limite passée à null au lieu de $CFG->courseperpage dans la fonction coursecat_ajax().<br/>
    Si besoin on peut aussi déclarer $CFG->courseperpage = null dans le fichier de config mais cela agira de manière globale sur le moodle (les liens "voir plus" et "voir moins" pour les catégories de cours ne seront du coup plus visibles).<br/><br/>
    <i>* les numéros de lignes indiqués pour les changements sont variables en fonction de la version utilisée.</i>
    
- L'option d'administration Site administration -> Cours -> Sauvegardes -> Sauvegarde automatique -> backup_auto_destination doit être renseignée (même si la sauvegarde automatique n'est pas activée, on se sert de ce dossier pour dézipper l'archive du cours) ! Ce dossier devant correspondre avec celui défini pour l'option d'administration transfer_archives_folder (cf. point 4.).
- Utilisation de l'option d'administration Site administration -> Utilisateurs -> Permissions -> Règles utilisateurs -> creatornewroleid. Il est conseillé que le rôle choisi ait des droits de gestion sur le course car c'est par défaut le rôle donné au demandeur du transfert.
- Attention: si des activités ont été ajoutées manuellement sur l'ancienne plateforme, celles-ci doivent aussi être présentes sur votre nouvelle plateforme pour que le transfert de cours soit complet.

Usages
-----
- Accéder à la page /local/uca_transfer_course/import.php pour faire afficher les demandes de transfert.<br/>
Les cours sont récupérés en fonction du username de la personne connectée (les usernames doivent donc correspondre, si besoin modifier le username à envoyer dans ce script).
- Choisir les cours à transferer.
- Cliquer sur "Transférer les cours sélectionnés". Une modal s'ouvre permettant de choisir la catégorie de cours dans laquelle transférer ces cours sélectionnés.
- Valider les demandes qui ont été ajoutées dans votre "panier". Les demandes passées et en cours sont visibles dans l'onglet "Historique des demandes".
- Faire tourner en cron le script /local/uca_transfer_courses/cli/import_task.php pour que les demandes soient traitées régulièrement et automatiquement ou lancer juste ce script à la main au besoin.
- Si tout s'est bien passé le cours est disponible dans la catégorie de cours selectionnée au moment de la demande.<br/> 

Dossier export
-----
- export.php: <br/>
Permet de sortir un json des cours dont l'utilisateur a les droits de gestion. Le json est ensuite utilisé dans la nouvelle plateforme pour permettre de sélectionner les cours à transférer.<br/>
Pour récupérer l'utilisateur, on passe son username par un requête http. Si besoin on pourra renseigner l'username en dur dans le script pour récupérer les cours.<br/>
Nous sommes partis du principe que l'on ne pouvait transférer que les cours dont on avait la gestion d'où un test sur le rôle "manager" dans la fonction getCourses() (à supprimer ou à modifier en fonction).

- import.php:<br/>
Permet de générer l'archive pour le cours que l'on veut transférer. On passera l'identifiant du cours par une requête http.<br/>
On renverra un json contenant le chemin vers cette archive.<br/>
2 actions sont possibles (paramètre "action" de la requête http envoyée): import qui permet donc de faire l'import et delete qui permettra de supprimer le dossier utilisé pour le transfert dans le but de ne pas engorger le dossier moodledata/.<br/> 
Certaines actions necessitent des droits spécifiques, l'idée est de simuler l'action d'un administrateur, il faudra donc renseigner l'id d'un des admins en début de script.

A propos
------
<a href="www.uca.fr">Université Clermont Auvergne</a> - 2018
