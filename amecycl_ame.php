<?php
/**
 * Configuration par defaut de la liste des types d'aménagements et des couleurs associées
 *
 * Pour la liste des types d'aménagements se référer à https://github.com/etalab/amenagements-cyclables/blob/master/schema_amenagements_cyclables.json
 * Le type AUCUN ne doit pas être pris en compte dans la liste
 *
 * @link              https://randovelo.touteslatitudes.fr
 * @since             1.0.0
 * @package           Amecycl
 *
 * En cas de modification (ajout d'un type et/ou changement de couleur) bien respecter la syntaxe php. Pour une prise en compte le plugin doit être desactivé puis activé.
 * Pour filtrer un type et/ou changer une couleur, il est préferable de créer une nouvelle configuration (voir le sous-menu gestion des configurations)
 *
 */
 
 /*  
  *  Parmi les catégories non prisent en compte sur amenagements-cyclables.fr ne sont pas pris en compte les catégories suivantes :
  *  "ACCOTEMENT REVETU HORS CVCB", "GOULOTTE", "AMENAGEMENT MIXTE PIETON VELO HORS VOIE VERTE"
  */


// liste des types d'aménagements et couleurs associées utilisés pour initialiser la configuration par défaut
// le type AUCUN est comptabilisé mais pas affiché sur la carte
$acy_default_ameColors = array(
	"ACCOTEMENT REVETU HORS CVCB" => '#00008B', // darkblue
	"AMENAGEMENT MIXTE PIETON VELO HORS VOIE VERTE" => '#FF00FF', // magenta
	"AUCUN" => '#FFFF00', // jaune
	"AUTRE" => '#4B0082', // indigo 
	"BANDE CYCLABLE" => '#FF0000',	// red
	"CHAUSSEE A VOIE CENTRALE BANALISEE" => '#808080', // grey
	"COULOIR BUS+VELO" => '#FFC300', // orange
	"DOUBLE SENS CYCLABLE BANDE" => '#4682B4', // steelblue
	"DOUBLE SENS CYCLABLE NON MATERIALISE" => '#00FFFF', // cyan
	"DOUBLE SENS CYCLABLE PISTE" => '#00BFFF', // deepskyblue
	"GOULOTTE" => '#FFC0CB', // pink
	"PISTE CYCLABLE" => '#000000', // black,
	"RAMPE" => '#FA8072', // salmon
	"VELO RUE" => '#7FFFD4', // aquamarine
	"VOIE VERTE" => '#008000'	// green
);

// liste des types d'aménagements et coefficient de calcul de longueur de la voie
// Si pour la voie il n'y a pas vraiment de distinction droite/gauche alors prendre 0.5 comme coefficient
// Ex: VOIE VERTE, VELO RUE, GOULOTTE, RAMPE, AUTRE (souvent des chemins)

$acy_ameLength_coeff = array(
	"ACCOTEMENT REVETU HORS CVCB" => 1, 
	"AMENAGEMENT MIXTE PIETON VELO HORS VOIE VERTE" => 0.5, 
	"AUCUN" => 1,
	"AUTRE" => 0.5, 
	"BANDE CYCLABLE" => 1,
	"CHAUSSEE A VOIE CENTRALE BANALISEE" => 1,
	"COULOIR BUS+VELO" => 1,
	"DOUBLE SENS CYCLABLE BANDE" => 1,
	"DOUBLE SENS CYCLABLE NON MATERIALISE" => 1,
	"DOUBLE SENS CYCLABLE PISTE" => 1,
	"GOULOTTE" => 0.5,
	"PISTE CYCLABLE" => 1,
	"RAMPE" => 0.5,
	"VELO RUE" => 0.5,
	"VOIE VERTE" => 0.5
);
