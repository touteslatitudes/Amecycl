<?php
/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://randovelo.touteslatitudes.fr
 * @since      1.0.0
 *
 * @package    Amecycl
 * @subpackage Amecycl/public/partials
 *
 */
?>
<div>
	<div id="sidebar-<?php echo $ame_rid;?>"></div>
    <div id="menu-<?php echo $ame_rid;?>" class="leaflet-sidebar collapsed">
      <!-- Nav tabs -->
      <div class="leaflet-sidebar-tabs">
        <ul role="tablist">
          <li>
            <a href="#home-<?php echo $ame_rid;?>" role="tab"><i class="fa fa-bars fa-lg"></i></a>
          </li>
          <li>
            <a href="#maps-<?php echo $ame_rid;?>" role="tab"><i class="fa fa-map-o fa-lg"></i></a>
          </li>
          <li>
            <a href="#ames-<?php echo $ame_rid;?>" role="tab"><i class="fa fa-bicycle fa-lg"></i></a>
          </li>
          <li>
            <a href="#downloads-<?php echo $ame_rid;?>" role="tab"><i class="fa fa-gear fa-lg"></i></a>
          </li>
        </ul>
      </div>
      <!-- Tab panes -->
      <div class="leaflet-sidebar-content">
        <div class="leaflet-sidebar-pane" id="home-<?php echo $ame_rid;?>">
          <h1 class="leaflet-sidebar-header">
            <?php echo __('Cycle routes','amecycl'); ?>
            <span class="leaflet-sidebar-close">
              <i class="fa fa-caret-right"></i>
            </span>
          </h1>
          <?php echo $ame_presentation; ?>
        </div>
        <div class="leaflet-sidebar-pane menu-maps" id="maps-<?php echo $ame_rid;?>">
          <h1 class="leaflet-sidebar-header">
            <?php echo __('Maps','amecycl'); ?>
			<span class="leaflet-sidebar-close">
              <i class="fa fa-caret-right"></i>
            </span>
          </h1>
		  <div id="select-maps-<?php echo $ame_rid;?>"></div>
        </div>
        <div class="leaflet-sidebar-pane" id="ames-<?php echo $ame_rid;?>">
          <h1 class="leaflet-sidebar-header">
            <?php echo __('Cycle route types','amecycl'); ?>
			<span class="leaflet-sidebar-close">
              <i class="fa fa-caret-right"></i>
            </span>
          </h1>
		  <div id="select-ames-<?php echo $ame_rid;?>" class="select-ames checkbox"><?php echo $ame_menu;?></div>
        </div>
        <div class="leaflet-sidebar-pane" id="downloads-<?php echo $ame_rid;?>">
          <h1 class="leaflet-sidebar-header">
            <?php echo __('Downloads','amecycl'); ?>
			<span class="leaflet-sidebar-close">
              <i class="fa fa-caret-right"></i>
            </span>
          </h1>
          <p><?php echo __('Download files :','amecycl');?></p>
          <div class="select-downloads"><?php echo $ame_download_list;?></div>
        </div>
      </div>
    </div><div id="map-<?php echo $ame_rid;?>" class="map"></div>
</div>