
# Plugin Wordpress Amecycl [![](https://img.shields.io/badge/wordpress-5.8+-blue.svg)](https://www.wordpress.org/downloads/)

Plugin Wordpress pour l'affichage des **aménagements cyclables** d'une région (EPCI)

Le plugin **affiche la carte** des aménagements cyclables de une ou plusieurs régions et **calcule la longueur et le linéaire cyclable** par type d'aménagements pour chaque région. 

Les types d'aménagements affichés sont les suivants :  

	"ACCOTEMENT REVETU HORS CVCB"
	"AMENAGEMENT MIXTE PIETON VELO HORS VOIE VERTE"
	"AUTRE" 
	"BANDE CYCLABLE" 
	"CHAUSSEE A VOIE CENTRALE BANALISEE" 
	"COULOIR BUS+VELO" 
	"DOUBLE SENS CYCLABLE BANDE" 
	"DOUBLE SENS CYCLABLE NON MATERIALISE" 
	"DOUBLE SENS CYCLABLE PISTE" 
	"GOULOTTE" 
	"PISTE CYCLABLE" 
	"RAMPE" 
	"VELO RUE" 
	"VOIE VERTE" 


Le menu droit de la carte permet de :  

- choisir un fond de carte (OpenStreetMap, CyclOsm, OpenTopoMap, Esri TopoMap, ...)
- filtrer l'affichage d'un type d'aménagement
- télécharger le calque correspondant à un aménagement au format .geojson

Un click sur un aménagement tracé sur la carte permet de voir les **caractéristiques de l'aménagement**


Le fichier de données nécessaire à la création doit être conforme à la version **0.3.3** du schéma de données des aménagements cyclables 

Pour le détail du schéma de données voir : 
* https://schema.data.gouv.fr/etalab/schema-amenagements-cyclables/ 
* https://github.com/etalab/amenagements-cyclables


## Installation du plugin Amecycl :

- télécharger le plugin (format .zip) et dézipper le fichier

- installer le répertoire amecycl dans le répertoire content/plugins de votre installation wordpress


## Utilisation du plugin :

- **activer le plugin Amecycl**  

Depuis le gestionnaire d'administration Wordpress, activer le plugin Aménagements Cyclables  


- **Créér une région**  

Dans le menu Amenagements cyclables  / Gestion des régions (situé en bas à gauche dans le gestionnaire
d'administration Wordpress) utiliser le bouton Ajouter pour créer une région.  

Important : Le fichier .geojson décrivant les aménagements cyclables doit être mis dans le répertoire amecycl/uploads à la racine de l'installation Wordpress
Soit vous utilisez ftp pour installer le fichier dans le répertoire sur le serveur soit vous utilisez l'upload fichier du sous menu.  
Pour les gros fichiers, privilégier ftp.  

Pour savoir comment récupérer le fichier des aménagements cyclables correspondant à une région (EPCI) voir https://randovelo.touteslatitudes.fr/amecycl/


- **Créér une configuration**  

La configuration par défaut retient l'affichage de tous les aménagements. Les couleurs associées sont choisies par défaut.  
Mais vous pouvez choisir de créer une configuration spécifique en écartant certains types d'aménagements (RAMPE, GOULOTTE, AUTRE ...) et en choisissant vos propres couleurs.


- **Utiliser les shortcodes amecycl et amecycl_stats**  

L'affichage de la carte se fait simplement via un shortcode :  

[amecycl slug='grand-paris-2021'] : affichage de la carte des aménagements cyclable du Grand Paris


L'affichage des statistiques se fait via le shortcode amecycl-stats :  

[amecycl-stats slugs='grand-paris-2021' width='50%' setting='geovelo' font-size='12px']  


Les paramètres des shortcodes :  

Pour l'**affichage de la carte** :
- slug='orleans-metropole' - identifiant textuel (slug)) correspondant à la région. **Paramètre obligatoire**  

- setting='geovelo' - identifiant textuel correspondant à une configuration. Par défaut la configuration choisie à la création de la région est utilisée.
                      Cela permet de changer de changer la configuration à la volée lors de l'affichage de la carte.  
					  
- width='500px' ou width='60%' - permet de définir la largeur de la carte. (100% par défaut)  

- heigth='700px' - permet de définir la hauteur de la carte. (700px par défaut)  

- deltazoom='1' - entier positif ou négatif - permet de modifier le zoom obtenu après l'affichage (0 par defaut)  
                  deltazoom permet notamment d'ajuster le zoom pour que plusieurs cartes affichées côte à côte présentent la même échelle.  


Pour l'**affichage du tableau des statistiques** :  

- slugs='orleans-metropole, tours-metropole, bourges-plus' - identifiants textuels (slug)) correspondant aux régions à comparer.  
                                                             L'identifiant textuel (slug) de au moins une région doit être donné. **Paramètre obligatoire**  

- setting='geovelo' - identifiant textuel correspondant à la configuration commune à utiliser pour l'affichage des statistiques.  
                      Par défaut la configuration choisie à la création de la région est utilisée.  
                      Cela permet d'uniformiser le tableau des statistiques pour des régions n'ayant pas la même configuration. 

- width='500px' ou width='60%' - permet de définir la largeur du tableau. (100% par défaut) 

- align='center' - permet d'aligner le tableau. Valeurs permises : left | center | rigth  (left par défaut) 

- font-size='11px" - taille des caractères de 8px à 24px - (12px par défaut)  
															 
															 
**Quelques exemples** de shortcodes : 

- Affichage de la carte des aménagements cyclables de la France  
[amecycl slug='france-2021' setting='amecycl']  

- Affichage des statistiques pour les aménagements cyclables de la France  
[amecycl-stats slugs='france-2021' setting='amecycl' width='50%' font-size='11px']  


- Affichage des cartes de Lille, Toulouse et Nantes côte à côte.  
[amecycl slug='lille-metropole-2021' setting='geovelo' width='32%'][amecycl slug='toulouse-metropole-2021' setting='geovelo' width='32%'][amecycl slug='nantes-metropole-2021' deltazoom='1' setting='geovelo' width='32%']  

- Affichage des statistiques de Lille, Toulouse et Nantes dans un seul tableau  
[amecycl-stats setting='geovelo' width='80%' slugs='lille-metropole-2021,toulouse-metropole-2021,nantes-metropole-2021']  


- Affichage des cartes de Fleury-les-Aubrais, Olivet, Orléans et Saint-Jean-de-Braye en carré (2 x 2 cartes)  
[amecycl slug='orleans-2021' deltazoom='1' setting="geovelo" width='49%'][amecycl setting='geovelo' deltazoom='-1' slug='fleury-les-aubrais-2021' width='49%']  

[amecycl setting='geovelo' slug='olivet-2021' width='49%'][amecycl setting='geovelo'  deltazoom='-1' slug='st-jean-de-braye-2021' width='49%']  

- Affichage des statistiques de Fleury-les-Aubrais, Olivet, Orléans et Saint-Jean-de-Brayes dans un seul tableau  
[amecycl-stats setting='geovelo' slugs='orleans-2021,fleury-les-aubrais-2021,olivet-2021,st-jean-de-braye-2021' align='left' width='80%']  


## Plus d'informations (en Français) : 

[Amecycl – Dessine moi les aménagements cyclables de ma ville … ](https://randovelo.touteslatitudes.fr/amecycl/)


## Versions : 

** Version 1.1.1 ** 
12/01/2022 

- correction d'un bug sur l'ordre d'affichage des aménagements dans le menu de selection 


** Version 1.1.0 ** 
07/01/2022 

- ajout de la surcharge de configuation 
- ajout des paramètres deltazoom et padding
- mise en forme des informations affichées 
- messages d'erreurs dans la saisie du shortcode
- optimisation du code (partie js)
- correction de bugs (partie admin) 


** Version 1.0.0 ** 
23/12/2021  

Version initiale 