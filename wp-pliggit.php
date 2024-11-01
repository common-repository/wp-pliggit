<?php

/*

Plugin Name: WP-Pligg.it
Version: 1.0.1
Plugin URI: http://blog.pligg.it
Description: Aggiunge un bottone ai vostri articoli per proporli e votarli su Pligg.it.
Usage: Tre modalità di utilizzo:

    Automatica: semplicemente scegliendo il posizionamento dalle Opzioni Pligg.it nel menù Opzioni.
    Nel tema: richiamando show_pliggit() nel vostro tema all'interno della LOOP.
    Nell'articolo: inserendo <!--pliggit--> in un punto qualsiasi dell'articolo.
Author: Bruno Ricci
Author URI: http://blog.pligg.it

Modified version of the WP-Sphinnit Plugin by
Michelle MacPhearson
http://blog.michellemacphearson.com
*/

$message = "";

if (!function_exists('smpligg_request_handler')) {
    function smpligg_request_handler() {
        global $message;

        if ($_POST['smpligg_action'] == "update options") {
            $smpligg_align_v = $_POST['smpligg_align_sl'];

    		if(get_option("smpligg_box_align")) {
    			update_option("smpligg_box_align", $smpligg_align_v);
    		} else {
    			add_option("smpligg_box_align", $smpligg_align_v);
    		}

            $message = '<br clear="all" /> <div id="message" class="updated fade"><p><strong>Opzioni salvate. </strong></p></div>';
        }
    }
}

if(!function_exists('smpligg_add_menu')) {
    function smpligg_add_menu () {
        add_options_page("Opzioni Pligg.it", "Opzioni Pligg.it", 8, basename(__FILE__), "smpligg_displayOptions");
    }
}

if (!function_exists('smpligg_displayOptions')) {
    function smpligg_displayOptions() {

        global $message;
        echo $message;

		print('<div class="wrap">');
		print('<h2>Opzioni Pligg.it</h2>');

        print ('<form name="smpligg_form" action="'. get_bloginfo("wpurl") . '/wp-admin/options-general.php?page=wp-pliggit.php' .'" method="post">');
?>

		<p>Posizionamento del bottone Pligg.it:
        <select name="smpligg_align_sl" id="smpligg_align_sl">
			<option value="Top Left"   <?php if (get_option("smpligg_box_align") == "Top Left") echo " selected"; ?> >In Alto a Sinistra</option>
			<option value="Top Right"   <?php if (get_option("smpligg_box_align") == "Top Right") echo " selected"; ?> >In Alto a Destra</option>
			<option value="Bottom Left"  <?php if (get_option("smpligg_box_align") == "Bottom Left") echo " selected"; ?> >In Basso a Sinistra</option>
			<option value="Bottom Right"  <?php if (get_option("smpligg_box_align") == "Bottom Right") echo " selected"; ?> >In Basso a Destra</option>
			<option value="None"  <?php if (get_option("smpligg_box_align") == "None") echo " selected"; ?> >Tramite Inserimento Codice</option>
		</select><br /><br /> </p>

<?php
		print ('<p><input type="submit" value="Salva &raquo;"></p>');
		print ('<input type="hidden" name="smpligg_action" value="update options" />');
		print('</form></div>');

    }
}


if (!function_exists('smpligg_pliggithtml')) {
	function smpligg_pliggithtml($float) {
		global $wp_query;
		$post = $wp_query->post;
		$permalink = get_permalink($post->ID);
        $title = urlencode($post->post_title);
		$pliggithtml = <<<CODE

    <span style="margin: 0px 6px 0px 0px; float: $float;">

	<script type="text/javascript">
	submit_url = "$permalink";
	</script>
    <script type="text/javascript" src="http://pligg.it/evb/button.php"></script>
	</span>
CODE;
	return  $pliggithtml;
	}
}


if (!function_exists('smpligg_addbutton')) {
	function smpligg_addbutton($content) {

		if ( !is_feed() && !is_page() && !is_archive() && !is_search() && !is_404() ) {
    		if(! preg_match('|<!--pliggit-->|', $content)) {
    		    $smpligg_align = get_option("smpligg_box_align");
    		    if ($smpligg_align) {
                    switch ($smpligg_align) {
                        case "Top Left":
        		              return smpligg_pliggithtml("left").$content;
                              break;
                        case "Top Right":
        		              return smpligg_pliggithtml("Right").$content;
                              break;
                        case "Bottom Left":
        		              return $content.smpligg_pliggithtml("left");
                              break;
                        case "Bottom Right":
        		              return $content.smpligg_pliggithtml("right");
                              break;
                        case "None":
        		              return $content;
                              break;
                        default:
        		              return smpligg_pliggithtml("left").$content;
                              break;
                    }
                } else {
        		      return smpligg_pliggithtml("left").$content;
                }

    		} else {
                  return str_replace('<!--pliggit-->', smpligg_pliggithtml(""), $content);
            }
        } else {
			return $content;
        }
	}
}

if (!function_exists('show_pliggit')) {
	function show_pliggit($float = "left") {
        global $post;
		$permalink = get_permalink($post->ID);
		echo <<<CODE

    <span style="margin: 0px 6px 0px 0px; float: $float;">

	<script type="text/javascript">
	submit_url = "$permalink";
	</script>
    <script type="text/javascript" src="http://pligg.it/evb/button.php"></script>
	</span>
CODE;
    }
}

add_filter('the_content', 'smpligg_addbutton', 999);
add_action('admin_menu', 'smpligg_add_menu');
add_action('init', 'smpligg_request_handler');

?>
