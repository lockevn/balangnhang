Prérequis:

Spécifications du serveur: Linux, Windows, Macos, Unix, Sun avec
- Apache 1.3.x ou supérieur, IIS6
- PHP 5.2 ou supérieur avec les fonctions overload(); et domxml(); (domxml uniquement pour php4) activé (Linux Mandriva n'est pas compilé par défaut avec la fonction overload and la FC4 a un bug à corriger)
- Mysql 4.1 ou supérieur
- Aucune importance si safe mode et register global sont actifs ou pas ;-)
- Si vous avez besoin de tester sur votre pc de maison sous windows nous conseillons easyphp 1.8

Procédure d'installation:

- Assurez-vous de disposer des paramètres FTP (serveur, identifiant, mot de passe) et des paramètres de votre base de données (identifiant, mot de passe, nom de la base)
- Si vous êtes sur votre pc de la maison avec votre easyphp 1.8 créez manuellement une base de données en allant à la page http://localhost/mysql/, rappelez-vous que l'identifiant pour la connexion à la base de données avec easyphp est "root" et le mot de passe est laissé vide
- Envoyez tous les fichiers à la racine de votre site
- Accédez à la page http://www.votresite.com/install/
- Suivre les instructions d'installation
- Une fois suivi toutes les étapes, c'est fini

Remarque: Le système va charger les fichiers XML de langues, attendre et ne pas cliquer sur quoi que ce soit avant que la page ait fini de charger !

Procédure de mise à jour depuis une version 2.0.x de docebo vers une version 3.x de docebo

Se reporter aux manuels

Procédure de mise à jour depuis une version 3.x de docebo vers une version plus récente:

- Ecraser tous les anciens fichiers (ne pas effacer le fichier config.php !!!!)
- Accéder à la page www.votresite.com/upgrade
- Suivre les instructions

Ajout de nouvelles langues (sans procédure de mise à jour)

- Se rendre dans la zone d'administration
- Accéder à la page configuration
- Puis choisir import/export de langue
- Importer les fichiers xml

Remarque: Cela écrasera tous les changements que vous auriez pu faire dans les fichiers de langues ! (exemple, si vous écrasez l'anglais vous perdrez toutes vos modifications en anglais)

Plus d'informations sur l'installation dans les manuels or wiki

http://www.docebo.org 