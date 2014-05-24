<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Page Type Builder - Property Google Map
 *
 * @package PageTypeBuilder
 * @version 1.0.0
 */

class PropertyMap extends PTB_Property {

  /**
   * Generate the HTML for the property.
   *
   * @since 1.0.0
   */

  public function html () {
    ?>
    <style type="text/css">
      #map-canvas {
        width: 100%;
        height: 400px;
      }
    </style>
    <div id="map-canvas"></div>
    <?php
  }

  /**
   * Output custom JavaScript for the property.
   *
   * @since 1.0.0
   * @throws PTB_Exception
   */

  public function js () {
    // Property settings.
    $settings = $this->get_settings(array(
      'api_key' => '',
      'latlng'  => ''
    ));

    if (!empty($settings->api_key)) {
      $api_key = $settings->api_key;
    } else {
      throw new PTB_Exception('Page Tyep Builder Error: You need to provide a api key for PropertyMap since we are using Google Maps');
    } ?>
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=<?php echo $api_key; ?>&sensor=false"></script>
    <script type="text/javascript">
      function updateLatitudeLangitude (position) {
        var el = document.querySelectorAll('input#<?php echo $this->get_options()->name; ?>');
        if (el.length) {
          el[0].value = [position.lat(), position.lng()].join(', ');
        }
      }

      function initialize() {
        <?php
          // Property options.
          $options = $this->get_options();

          // Database value.
          $value = $this->get_value();

          if (is_null($value) || empty($value)) {
            if (!empty($settings->latlng)) {
              $value = explode(',', trim($settings->latlng));
              $lat = $value[0];
              $lng = $value[1];
            } else {
              $lat = '59.32893';
              $lng = '18.06491';
            }
          } else {
            $value = explode(',', trim($value));
            $lat = $value[0];
            $lng = $value[1];
          }
        ?>
        var ptbLatLng = new google.maps.LatLng(<?php echo $lat; ?>, <?php echo $lng; ?>);

        var mapOptions = {
          center: ptbLatLng,
          zoom: 14
        };

        var map = new google.maps.Map(document.getElementById("map-canvas"), mapOptions);

        var marker = new google.maps.Marker({
          position: ptbLatLng,
          map: map,
          draggable: true,
          title: 'Select position'
        });

        google.maps.event.addListener(marker, 'drag', function() {
          updateLatitudeLangitude(marker.getPosition());
        });

        google.maps.event.addListener(marker, 'dragend', function() {
          updateLatitudeLangitude(marker.getPosition());
        });
      }
      google.maps.event.addDomListener(window, 'load', initialize);
    </script>
    <?php
  }

  /**
   * Generate the html for the "place" input.
   *
   * @since 1.0.0
   */

  public function input () {
    echo PTB_Html::input('text', array(
      'name' => $this->get_options()->name,
      'id' => $this->get_options()->name,
      'class' => $this->css_classes('ptb-halfwidth'),
      'value' => $this->get_options()->value
    ));
  }

  /**
   * Render the final html that is displayed in the table.
   *
   * @since 1.0.0
   */

  public function render () {
    $options = $this->get_options();
    if ($options->table): ?>
      <tr>
        <td colspan="2">
          <?php $this->html(); ?>
        </td>
      </tr>
      <tr>
        <td>
          <?php $this->label(); ?>
        </td>
        <td>
          <?php $this->input(); ?>
        </td>
      </tr>
      <?php
      $this->helptext();
    else:
      $this->html();
      $this->label();
      $this->input();
      $this->helptext();
    endif;
  }
}