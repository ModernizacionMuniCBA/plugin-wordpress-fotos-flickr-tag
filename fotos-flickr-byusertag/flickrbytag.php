<?php
/*
Plugin Name: Flickr by User & Tag
Plugin URI: https://github.com/ModernizacionMuniCBA/
Description: Este plugin muestra la fotos de un usuario de flickr filtradas por tag se utiliza con shortcode [muestra_fotos usuario=XXXX tag=XXXX]
Version: 1.5.3
Author: Ignacio Perlo
Author URI: 
*/

setlocale(LC_ALL,"es_ES");
date_default_timezone_set('America/Argentina/Cordoba');

add_action('plugins_loaded', array('flickrbytag', 'get_instancia'));

class flickrbytag
{
	public static $instancia = null;

	
	private static $URL_API_GOB_AB = "https://api.flickr.com/services/rest/?method=flickr.photos.search&api_key=fe2c0442bc28d6e81c30ff53e9b4d9df&user_id={usuario}&tags={tag}&per_page=40000&format=json&nojsoncallback=1";
	

	public static function get_instancia() {
		if (null == self::$instancia) {
			self::$instancia = new flickrbytag();
		} 
		return self::$instancia;
	}
	
	
	private function __construct()
	{
		
		
		add_shortcode('muestra_fotos', array($this, 'render_shortcode_muestra_fotos'));
		add_action('wp_enqueue_scripts', array($this, 'cargar_assets'));
		add_action('init', array($this, 'boton_shortcode_flickr_by_tag'));
	}
	
	public function cargar_assets()
	{
		$urlCSSShortcode = $this->cargar_url_asset('/css/shortcode.css');
		 wp_register_style('flickr_by_tag_css', $urlCSSShortcode);
		 wp_enqueue_style('flickr_by_tag_css', $urlCSSShortcode);
		 
		 $urlJSCargafotos = $this->cargar_url_asset('/js/cargar-fotos.js');
		 
		 wp_register_script('carga-fotos', $urlJSCargafotos,null,false,false);
		 wp_enqueue_script('carga-fotos',$urlJSCargafotos,null,false,false);
			
		 
		
		
	}
	private function cargar_url_asset($ruta_archivo)
	{
		return plugins_url($ruta_archivo, __FILE__);
	}
	
	public function chequear_respuesta($api_response, $tipoObjeto, $nombre_transient)
	{
		if (is_null($api_response)) {
			return [ 'results' => [] ];
		} else if (is_wp_error($api_response)) {
			return [ 'results' => [], 'error' => 'Ocurri&oacute; un error al cargar '.$tipoObjeto.'.'.$mensaje];
		} else {
			$respuesta = json_decode(wp_remote_retrieve_body($api_response), true);
			return $respuesta;
		}
	}
	
	public function render_shortcode_muestra_fotos($atributos = [], $content = null, $tag = '')
	{
	    $atributos = array_change_key_case((array)$atributos, CASE_LOWER);
	    $atr = shortcode_atts([
            'usuario' => '',
            'tag' => '',
			'cant' => 0      
			], $atributos, $tag);

	    
		$filtro_cantidad = $atr['cant'] == 0 ? 40 : $atr['cant'];
		
		$url=self::$URL_API_GOB_AB;
	    $url = str_replace("{usuario}",$atr['usuario'],$url);
		$url = str_replace("{tag}",$atr['tag'],$url);
		if(isset($_REQUEST['page']))
		{
			$pagina=$_REQUEST['page'];	
		}else{
			$pagina=1;	
		}
		$url = str_replace("{pagina}",$pagina,$url);
		
		//echo $url;

    	$api_response = wp_remote_get($url);
    	//$nombre_transient = 'actividades_disciplina_' . $atr['disciplina'];
		
		$resultado=json_decode(wp_remote_retrieve_body($api_response),true);
		//var_dump($resultado['photos']);
		$divid=rand();
		$mas="javascript:mas('".$atr['usuario']."',".$filtro_cantidad.",'".$divid."')";
		
		$sc = '<div id="resultados_'.$divid.'">	
			   </div>
			   <div id="cargarmas_'.$divid.'" class="cargarmas"><a href="'.$mas.'">Cargar mas</a></div>
			   <script type="text/javascript">
			   var fotos;';
		$lista="";
		if (count($resultado['photos']["photo"]) > 0) {
			
			foreach ($resultado['photos']["photo"] as $key => $foto) {
				$lista.=$foto['id'].",".$foto['secret'].",".$foto['server'].";";
			}
		}
		$fin=$filtro_cantidad-1;
		$sc.='	var inicio=0;
				var fin='.$fin.';
				lista_'.$divid.'="'.substr($lista,0,strlen($lista)-1).'";
				
				cargar(inicio,fin,"'.$atr['usuario'].'","'.$divid.'");
				</script>';
		return $sc;
	}

	public function boton_shortcode_flickr_by_tag() {
		if (!current_user_can('edit_posts') && !current_user_can('edit_pages') && get_user_option('rich_editing') == 'true')
			return;

		add_filter("mce_external_plugins", array($this, "registrar_tinymce_plugin")); 
		add_filter('mce_buttons', array($this, 'agregar_boton_tinymce_shortcode_flickr_by_tag'));
	}

		public function registrar_tinymce_plugin($plugin_array) {
		$plugin_array['flickr_button'] = $this->cargar_url_asset('/js/shortcode.js');
	    return $plugin_array;
	}

	public function agregar_boton_tinymce_shortcode_flickr_by_tag($buttons) {
	    $buttons[] = "flickr_button";
	    return $buttons;
	}
	
	
	
	
}
