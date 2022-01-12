/*
 *  Project: Amecycl
 *  Description: Affichage sur une carte et statistiques du linéaire des aménagements cyclables d'un territoire. 
 *  Author: toutesLatitudes
 *  License: GPL v3
 */

;(function ($, window, document, undefined) {

	"use strict";

	//  Les clés d'API IGN, OpenCycleMap, GoogleMap sont à définir dans le script amecycl.php
	const IGN_URL = 'https://wxs.ign.fr/{ignApiKey}/geoportail/wmts?' +
		'&REQUEST=GetTile&SERVICE=WMTS&VERSION=1.0.0&TILEMATRIXSET=PM' +
		'&LAYER={ignLayer}&STYLE=normal&FORMAT={format}' +
		'&TILECOL={x}&TILEROW={y}&TILEMATRIX={z}'
	const IGN_ATTRIB = '&copy; <a href="http://www.ign.fr/">IGN</a>'

    var // plugin name
        pluginName = "acyLeaflet",
        // key using in $.data()
        dataKey = "plugin_" + pluginName;
		
	// gestion de plusieurs instances de plugin sur une meme page
	// chaque instance de carte est identifiée par l'id de la region affichée
	var tmap = [];		// tableau des variables map initialisées
	var tsidebar = [];	// tableau des variables sidebar initialisés
		
	// affichage des aménagements
	var _setAmes = function(options, data) {

		// construction des couches type d'amenagements
		if (data.length) {

			var ameLayers = [];
			for (var i = 0, len = data.length; i < len; i++) {
				var layers = [];
				var amelayer = JSON.parse(data[i]);
				for (var j = 0, len2 = amelayer.features.length; j < len2; j++) {

					// creation des amenagements de même type
					amelayer.features[j].rid = options.rid; // on ajoute l'identifiant de la region à la feature
					layers[j] = L.geoJson(amelayer.features[j], {
						style: function(feature) {
							return _getAmeStyle(options, feature);
						},
						// affichage des infos de la voie
						onEachFeature: function(feature, layer) {
							return _getAmeDesc(options, feature, layer);
						}
					});
				}
				ameLayers[amelayer.name] = L.layerGroup(layers);
				// création d'une couche
				tmap[options.rid].addLayer(ameLayers[amelayer.name]);	// ajout de la couche name à la carte
			}
		}

		// ajout de la gestion de la selection à chacune des couches
		$('#select-ames-' + options.rid + ' > ul :checkbox').on('change', function(event) {

			// on deduit l'id de la region de l'id de la checkbox bande-cyclable-1 =>  1
			var strid = this.id;	
			var rid = strid.substr(strid.lastIndexOf("-")+1);
			
			//si la case est cochée on affiche la couche
			if (this.checked) {
				tmap[rid].addLayer(ameLayers[this.name]);
			}
			// sinon on masque la couche
			else {
				tmap[rid].removeLayer(ameLayers[this.name]);
			}

		});
		// on coche toutes les cases au début
		$('#select-ames-' + options.rid + ' > ul :checkbox').prop('checked', true);

	}

	// coloration des amenagements
	var _getAmeStyle = function(options, feature) {
		switch (feature.properties.ame_d) {
			case 'AUCUN':
			    if (options.ameColors.hasOwnProperty(feature.properties.ame_g)) {
					var color = options.ameColors[feature.properties.ame_g];
				}
				else {
					var color = 'SandyBrown';
				}
				break;
			default:
			    if (options.ameColors.hasOwnProperty(feature.properties.ame_d)) {
					var color = options.ameColors[feature.properties.ame_d];
				}
				else {
					var color = 'SandyBrown';
				}
				break;
		}
		return {
			"color": color,
			"weight": options.ameWeight,
			"opacity": options.ameOpacity
		}
	}

	// affichage dans le sidebar de gauche de la description d'un amenagement
	var _getAmeDesc = function(options, feature, layer) {
		if (feature.properties) {
			// ajout de l'event listener click. Ouverture du sidebar
			layer.on('click', function() {
				var json = feature.properties;
				var rid = feature.rid;	// la feature porte le rid de la region

				var table = document.createElement("table");
				table.className = 'acy-table';

				for (var item in json) {
					var tr = table.insertRow(-1);
					var tabCell = tr.insertCell(-1);
					tabCell.className = 'acy-table';
					tabCell.innerHTML = item;
					var tabCell = tr.insertCell(-1);
					tabCell.className = 'acy-table';
					tabCell.innerHTML = json[item];
				}

				tsidebar[rid].hide();

				var divContainer = document.getElementById("sidebar-"+options.rid);
				divContainer.innerHTML = "";
				divContainer.appendChild(table);

				tsidebar[rid].show();

			});
		}
	}


    var Plugin = function (element, options) {

        this.element = element;
        
        this.defaults = {
            // default options
            rid: '0',						// id de la region = id de l'instance de carte affichée
			sid: '0',						// id de la configuration à appliquer (filtrage)
            bnd1: [53.540307,-11.030273], 	// [bnd1,bnd2] par defaut la boite englobant la france métropolitaine
            bnd2: [39.095963,17.094727],
            maps: ['OSM'], 					// choix des cartes parmi 'OSM','COSM','SCAN25','IGN25','PHOTOS'
			width: '100%',					// largeur de la carte
			height: '500px',				// hauteur de la carte
			padding: 3,						// valeur absolue du padding
			deltazoom: 0,					// delta zoom à appliquer après affichage de la carte
            scale: true,					// affichage de l'echelle
            fullscreen: true,				// mode plein ecran possible
            geocoder: true,					// recherche
            menu: true,						// sidebar à droite
            ameWeight: 2,					// epaisseur du tracé des amenagements
            ameOpacity: 0.85,				// opacité du tracé des aménagements
            ameDash: '5,5',					// traitillé
			iak: '',						// ign api key
			ameColors: acyvar.ameColors,	// couleurs associées aux types d'aménagements
		};
		               
		this.init(options);	// Initialisation
    };

    Plugin.prototype = {
        // initialize options
        init: function (options) {
            options = $.extend(this.defaults, options);
            
            var basemap = {};
			var iLayer = 'OpenStreetMap';
					
            for (var i = 0, len = options.maps.length; i < len; i++) {
				switch (options.maps[i]) {

					// Google Maps
                    case "GMRMP":
                        // Google Maps RoadMaP
                        var googleLayer = new L.gridLayer.googleMutant({ type: 'roadmap'});
                        basemap['Google Roadmap'] = new L.gridLayer.googleMutant({
                            type: 'roadmap',
                             minZoom: 5,
                            maxZoom: 18
                        });
                        if (i == 0) iLayer = 'Google Roadmap';
                        break;

                    case "GMSAT":
                        // Google Maps SATellite
                        basemap['Google Satellite'] = new L.gridLayer.googleMutant({
                            type: 'satellite',
                            minZoom: 5,
                            maxZoom: 18
                        });
                        if (i == 0) iLayer = 'Google Satellite';
                        break;

                    case "GMTER":
                        // Google Maps TERrain
                        basemap['Google Terrain'] = new L.gridLayer.googleMutant({
                            type: 'terrain',
                            minZoom: 5,
                            maxZoom: 18
                        });
                        if (i == 0) iLayer = 'Google Terrain';
                        break;

					// ESRI
					case "ETOPO":
						// ESRI World Topographic Map (https://services.arcgisonline.com/ArcGIS/rest/services/World_Topo_Map/MapServer)
						var esriUrl = 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Topo_Map/MapServer/tile/{z}/{y}/{x}';
						var esriAttrib = '&copy; <a href="https://esriurl.com/WorldTopographicMapContributors">Esri World Topographic Map</a> contributors';

					    basemap['ESRI Topo Map'] = new L.tileLayer(esriUrl, {
							minZoom: 5,
							maxZoom: 18,
							attribution: esriAttrib
						});
                        if (i == 0) iLayer = 'ESRI Topo Map';
                        break;

					case "EIMAG":
						// ESRI World Imagery (https://services.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer)
						var esriUrl = 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}'
						var esriAttrib = '&copy; <a href="https://esriurl.com/WorldImageryContributors">Esri World Imagery</a> contributors'

						basemap['ESRI Imagery'] = new L.TileLayer(esriUrl, {
							minZoom: 5,
							maxZoom: 18,
							attribution: esriAttrib
						});
                        if (i == 0) iLayer = 'ESRI Imagery';
                        break;

					// IGN
					case "MAPS":
						// IGN cartes standard
						basemap['IGN Cartes'] = new L.tileLayer(IGN_URL, {
							ignApiKey: options.iak,
							ignLayer: 'GEOGRAPHICALGRIDSYSTEMS.MAPS',
							format: 'image/jpeg',
							opacity: 1, minZoom: 3, maxZoom: 20, attribution: IGN_ATTRIB
						});
						if (i == 0) iLayer = 'IGN Cartes';
						break;

					case "SCAN25":
						// IGN Topo Scan 25
						/* La carte topographique représente avec précision le relief, symbolisé par des courbes de niveaux, ainsi que les détails du terrain : 
						routes, sentiers, constructions, bois, arbre isolé, rivière, source... En France, la carte topographique de base est réalisée par l'IGN. 
						Le SCAN 25 Touristique comprend les pictogrammes du thème tourisme de la carte de base. */
						basemap['IGN Topo'] = new L.tileLayer(IGN_URL, {
							ignApiKey: options.iak,
							ignLayer: 'GEOGRAPHICALGRIDSYSTEMS.MAPS.SCAN25TOUR',
							format: 'image/jpeg',
							opacity: 1, minZoom: 5, maxZoom: 18, attribution: IGN_ATTRIB
						});
						if (i == 0) iLayer = 'IGN Topo';
						break;

					case "PLANS":
						// IGN PLANS
						basemap['IGN Plans'] = new L.tileLayer(IGN_URL, {
							ignApiKey: 'essentiels',
							ignLayer: 'GEOGRAPHICALGRIDSYSTEMS.PLANIGNV2',
							format: 'image/png',
							opacity: 1, minZoom: 5, maxZoom: 18, attribution: IGN_ATTRIB
					    });
						if (i == 0) iLayer = 'IGN Plans';
						break;

					case "ORTHO":
					// IGN ORTHOPHOTOS
						basemap['IGN Orthophotos'] = new L.tileLayer(IGN_URL, {
						   ignApiKey: 'essentiels',
						   ignLayer: 'ORTHOIMAGERY.ORTHOPHOTOS',
						   format: 'image/jpeg',
						   opacity: 1, minZoom: 5, maxZoom: 18, attribution: IGN_ATTRIB
					    });
						if (i == 0) iLayer = 'IGN Orthophotos';
						break;

					// OpenCycleMap
					case "OCM":
					// OpenCycleMap - https://tile.thunderforest.com/cycle/{z}/{x}/{y}.png?apikey=<insert-your-apikey-here>
						var ocmUrl = 'https://tile.thunderforest.com/cycle/{z}/{x}/{y}.png?apikey=' + options.oak;
						var ocmAttrib = '&copy; <a href="https://www.thunderforest.com/maps/opencyclemap/">OpenCycleMap</a>';
						basemap['OpenCycleMap'] = L.tileLayer(ocmUrl, {
							minZoom: 5,
							maxZoom: 18,
							attribution: ocmAttrib
						});
						if (i == 0) iLayer = 'OpenCycleMap';
						break;

					// OpenTopoMap
					case "OTM":
					// OpenTopoMap - https://tile.thunderforest.com/cycle/{z}/{x}/{y}.png?apikey=<insert-your-apikey-here>
						var otmUrl = 'https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png';
						var otmAttrib = '&copy; <a href="https://www.opentopomap.org/">OpenTopoMap</a> contributors';
						basemap['OpenTopoMap'] = new L.TileLayer(otmUrl, {
							minZoom: 5,
							maxZoom: 18,
							attribution: otmAttrib
						});
						if (i == 0) iLayer = 'OpenTopoMap';
						break;

					// OpenStreetMap
					case "OSM":
					// OpenStreetMap - https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png
						var osmUrl = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
						var osmAttrib = '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors';
						basemap['OpenStreetMap'] = new L.TileLayer(osmUrl, {
							minZoom: 5,
							maxZoom: 18,
							attribution: osmAttrib
						});
						if (i == 0) iLayer = 'OpenStreetMap';
						break;

					// CyclOSM
					case "COSM":
					default:
					// CyclOSM - https://{s}.tile-cyclosm.openstreetmap.fr/cyclosm/{z}/{x}/{y}.png
						var cosmUrl = 'https://{s}.tile-cyclosm.openstreetmap.fr/cyclosm//{z}/{x}/{y}.png';
						var cosmAttrib = '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors';
						basemap['CyclOSM'] = L.tileLayer(cosmUrl, {
							minZoom: 5,
							maxZoom: 18,
							attribution: cosmAttrib
						});
						if (i == 0) iLayer = 'CyclOSM';
						break;
				}
			}

			// ---------------------------------------- affichage de la carte
			// adaptation de la dimension du div map
					var mapid = 'map-' + options.rid;
					document.getElementById(mapid).style.width = options.width;
					document.getElementById(mapid).style.height = options.height;

					// creation de la carte
                    var map = L.map(mapid, { layers: [basemap[iLayer]] });

					var padding;
					if (isNaN(options.padding)){
						padding = 3;
					}
					else {
						padding = Math.abs(options.padding);
					}
					padding = Math.abs(options.padding);

                    map.fitBounds([options.bnd1, options.bnd2], {
                        padding: [-padding, -padding]  // ajout d'un padding negatif
                    }); 
					
					// ajustement du zoom
					var czoom = map.getZoom();
					var dzoom = parseInt(options.deltazoom);
					var nzoom = czoom + dzoom;
					if (nzoom !== czoom) map.setZoom(nzoom);

					tmap[options.rid] = map;
					
                    // ---------------------------------------- affichage de l'échelle
                    if (options.scale) {
                        L.control.scale({
                            imperial: false
                        }).addTo(map);
                    }

                    // ---------------------------------------- ajout du selecteur de cartes
                    if (options.maps.length > 1) {
                        var ctrlmaps = L.control.layers(basemap,{},{collapsed: false});
                        ctrlmaps.addTo(map);
                        var html = ctrlmaps.getContainer();
                        $('#select-maps-' + options.rid).html(html);

                    }

                    // ---------------------------------------- ajout de la fonction fullscreen
                    if (options.fullscreen) {
                        L.control.fullscreen({
                            title: {
                                'false': 'Affichage plein écran',
                                'true': 'Sortir de l\'affichage plein écran'
                            },
                            ctrl: 'menu'
                        }).addTo(map);
                    }

                    // ---------------------------------------- ajout de la fonction de recherche geocoder
                    if (options.geocoder) {
                        var osmGeocoder = new L.Control.OSMGeocoder({
                            collapsed: true, /* Whether its collapsed or not */
							position: 'topleft', /* The position of the control */
							text: 'Rechercher' /* The text of the submit button */
                        });
                        map.addControl(osmGeocoder);
                    }

                    // ---------------------------------------- ajout du sidebar lateral pop-up gauche
                    var sidebar = L.control.sidebar1('sidebar-' + options.rid).addTo(map);
					
					tsidebar[options.rid] = sidebar;

                    // ---------------------------------------- ajout de la barre de menu droite
                    if (options.menu) {
                        var menu = L.control.sidebar({ container: 'menu-' + options.rid, position: "right" }).addTo(map);
					}


                    // ---------------------------------------- gestion des evenements (click, zoomend, moveend, dragend)
                    if ("undefined" !== typeof options.click) {
                        if ("function" === typeof options.click) {
                            map.on('click', options.click);
                        }
                    }

                    if ("undefined" !== typeof options.zoomend) {
                        if ("function" === typeof options.zoomend) {
							map.on('zoomend', options.zoomend);
                        }
                    }

                    if ("undefined" !== typeof options.moveend) {
                        if ("function" === typeof options.moveend) {
                            map.on('moveend', options.moveend);
                        }
                    }

			},
        
			// ---------------------------------------- Affichage des amenagements
			addAmes: function(options) {
				options = $.extend(this.defaults, options);	
				( function(options) {  // closure
					// important: la closure est nécessaire pour mémoriser la valeur de options à la reception des données
					$.ajax({
							type: 'post',
							url: acyvar.url + 'wp-admin/admin-ajax.php',
							data: {
								action: 'get-ames',
								rid: options.rid,
								sid: options.sid
							},
							dataType: 'json',
							success: function(jsonData) {
								if (jsonData) _setAmes(options, jsonData); // affichage des amenagements
							}
						});
				}) (options); // fin de closure
			},
			
            // ---------------------------------------- callback appelee a la fin du zoom
            onZoomend: function(callback) {
					return this.each(function() {
						if ("undefined" !== typeof callback) {
							if ("function" === typeof callback) {
								map.on('zoomend', callback);
							}
						}
					});
			},
            // ---------------------------------------- callback appelee a la fin de deplacement
            onMoveend: function(callback) {
					return this.each(function() {
						if ("undefined" !== typeof callback) {
							if ("function" === typeof callback) {
								map.on('moveend', callback);
							}
						}
					});
			},
            // ---------------------------------------- callback appelee lors d'un click
            onClick: function(callback) {
					return this.each(function() {
						if ("undefined" !== typeof callback) {
							if ("function" === typeof callback) {
								map.on('click', callback);
							}
						}
					});
			}
    };

    $.fn[pluginName] = function (options) {

        var plugin = this.data(dataKey);

        // has plugin instantiated ?
        if (plugin instanceof Plugin) {
            // if have options arguments, call plugin.init() again
            if (typeof options !== 'undefined') {
                plugin.init(options);
            }
        } else {
            plugin = new Plugin(this, options);
            this.data(dataKey, plugin);
        }
        
        return plugin;
    };

}(jQuery, window, document));