<?php
/*
* Template Name: ABAP Analyzer App
*
* @author Cristian Marin
* @package ABAPAnalyzer
*/
?>
<!DOCTYPE html>
<!--[if IE 6]>
<html id="ie6" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 7]>
<html id="ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html id="ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 6) | !(IE 7) | !(IE 8)  ]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->

<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	<title><?php
		/*
		 * Print the <title> tag based on what is being viewed.
		 */
		global $page, $paged;
		wp_title( '|', true, 'right' );
		// Add the blog name.
		bloginfo( 'name' );
		// Add the blog description for the home/front page.
		$site_description = get_bloginfo( 'description', 'display' );
		if ( $site_description && ( is_home() || is_front_page() ) )
			echo " | $site_description";
		?>
	</title>
	<?php
		function aa_register_frontend_style(){
			wp_register_style(
				"aa_client_style",
				plugins_url( 'css/client/aa-shortcode.css' , __FILE__ ),
				null,
				"1.0",
				"all"
			);
			wp_enqueue_style("aa_client_style" );
		}
//		add_action( 'wp_enqueue_scripts', 'aa_register_frontend_style' );

//	add_action('init', 'aa_head_cleanup');
//	function aa_head_cleanup() {
		remove_action( 'wp_head', 'feed_links_extra', 3 );                      // Category Feeds
		remove_action( 'wp_head', 'feed_links', 2 );                            // Post and Comment Feeds
		remove_action( 'wp_head', 'rsd_link' );                                 // EditURI link
//		remove_action( 'wp_head', 'wlwmanifest_link' );                         // Windows Live Writer
//		remove_action( 'wp_head', 'index_rel_link' );                           // index link
		remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 );              // previous link
		remove_action( 'wp_head', 'start_post_rel_link', 10, 0 );               // start link
		remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );   // Links for Adjacent Posts
		remove_action( 'wp_head', 'wp_generator' );                             // WP version
		remove_action( 'admin_print_styles', 'print_emoji_styles' );
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
//		remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
//		remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
//		remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
//		wp_dequeue_script('page_volver_arriba');

		add_filter('show_admin_bar', '__return_false');							//remove the admin_bar fucntion
		remove_action('wp_head', '_admin_bar_bump_cb');							//remove the admin_bar style (html: padding)
		echo do_shortcode("[abap_analyzer]");
		
	wp_head();
	?>
	<link rel="profile" href="http://gmpg.org/xfn/11" />
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
	<!--[if lt IE 9]>
	<script src="<?php echo get_template_directory_uri(); ?>/js/html5.js" type="text/javascript"></script>
	<![endif]-->
</head>
<body class="aa-body">
<!-- Landing -->
	<div id="intro" class="hidden">

	<!-- Intro Header -->
		<header id="intro-header" class="container">
			<div id="intro-skip"><a href="#system-list" class="lead">Ingresar <i class="fa fa-sign-in fa-fw" aria-hidden="true"></i></a></div>
			<div class="logo animated flipInX">
				<img class="center-block" src="<?php echo plugins_url( 'img/aa_logo.svg', __FILE__ );?>" alt=""/>
			</div>
			<h1 class="text-center animated fadeInUp"><strong>ABAP</strong>Analyzer</h1>
			<p class="text-center lead animated fadeInLeft">Una nueva herramienta de an&aacute;lisis y revisión de sistemas SAP NW AS ABAP.</p>
		</header>		
	<!-- Intro Nav -->
		<div id="intro-header-space">
			<nav class="navbar navbar-default container" id="intro-nav">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="#">
						<span class="text"><strong>Abap</strong>Analyzer</span>
					</a>
				</div>
				<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
					<ul class="nav navbar-nav">
						<li name="intro-intro"><a href="#intro-intro">Introducci&oacute;n <span class="sr-only">(current)</span></a></li>
						<li name="intro-sdfmon"><a href="#intro-sdfmon">Snapshot Monitoring</a></li>
						<li name="intro-start"><a href="#intro-start">Comenzar</a></li>
					</ul>
				</div>
			</nav>	
		</div>
	<!-- Intro Main -->	
		<div id="intro-main" class="container">
		<!-- Intro -->
			<section id="intro-intro" class="clearfix active">
				<div id="chartdiv" class="col-md-8 col-md-push-4"></div>
				<div class="col-md-4 col-md-pull-8">
					<h2>Administración hecha simple</h2>
					<p class="lead text-center">Una herramienta que te permite enfocarte en lo que importa.</p>
				</div>
				<div class="col-xs-12">
					<p>La administraci&oacute;n de un sistema SAP NW AS ABAP es <strong>un reto</strong>. Diferentes fuentes de informaci&oacute;n, y configuraciones complejas para obtenerla, impiden realizar tareas de administración enfocadas en lo que es importante: la satisfacción del cliente.</p>
				<p>Con <a href="#"><strong>ABAP</strong>Analyzer</a> puedes obtener una an&aacute;lisis completo, con tan solo la habilitación de colectores nativos del sistema. No necesitas SAP Solution Manager, ni instalar agentes, ni configurar condiciones de monitoreo complejo.</p>
				</div>
			</section>
			<section id="intro-sdfmon" class="">
				<h2>Snapshot Monitoring <small>(/sdf/mon)</small></h2>
				<p class="lead text-center">Explota la información del Snapshot Monitoring con tan solo unos clicks.</p>
				<ul class="features">
					<li class="col-xs-max col-sm-4">
						<i class="style1 fa fa-dashboard fa-5x fa-fw"></i>
						<h3>Workprocess</h3>
						<p>Utilizaci&oacute;n real de Workprocess en el sistema.</p>
					</li>
					<li class="col-xs-max col-sm-4">
						<i class="style2 fa fa-copy fa-5x fa-fw"></i>
						<h3>Memoria</h3>
						<p>Utilización de los diferentes segmentos de memoria SAP.</p>
					</li>
					<li class="col-xs-max col-sm-4">
						<i class="style3 fa fa-clock-o fa-5x fa-fw"></i>
						<h3>Tiempo Real</h3>
						<p>Granularidad basada en muestras cada 30 segundos del sistema.</p>
					</li>
				</ul>
				<div class="more text-center clearfix">
					<a href="#sdfmon-instructions" class="text-center btn btn-default">Aprender m&aacute;s</a>
				</div>
			</section>
			<section id="intro-start" class="">
				<h2>Comienza a usar <strong>ABAP</strong>Analyzer</small></h2>
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras condimentum massa massa, vitae mollis dolor sodales at. Donec sapien sapien, congue eget metus id, ornare blandit justo. Aenean volutpat placerat aliquet. Etiam commodo lacus in velit accumsan, sed ornare ipsum tincidunt. Proin purus orci, molestie non bibendum eget, sollicitudin a nisi. Etiam sed sem urna. Curabitur consequat porttitor vestibulum. Suspendisse varius eu elit sed bibendum.</p>
				<p>Etiam cursus sagittis tincidunt. Donec suscipit pharetra est. Mauris fringilla tortor ac nibh imperdiet porta. Nunc sit amet dolor egestas, elementum nibh in, posuere enim. Vestibulum lectus justo, suscipit sed placerat vitae, malesuada nec nunc. Praesent imperdiet ante nisi, eu iaculis ante dignissim sit amet. Integer sed euismod eros. Vivamus fringilla, diam at eleifend laoreet, augue mauris consequat massa, ac rutrum elit sem a felis.</p>
				<div class="more text-center clearfix">
					<a href="#system-list" class="text-center btn btn-success btn-lg"><i class="fa fa-power-off fa-fw" aria-hidden="true"></i> Entra al sistema</a>
				</div>
			</section>
		</div>
	<!-- Intro Footer -->
		<div id="intro-footer" class="container clearfix">
			<section class="col-md-6">
				<h3>Canal de Yammer</h3>
				<addres>
					<p>Sigue el canal de Yammer dentro de <a href="http://noviscorp.com" target="_blank">Noviscorp.com</a> para obtener casos de uso, seguimiento a mejoras y/o reportar problemas.</p>
				</addres>
				<a href="https://www.yammer.com/noviscorp.com/#/threads/inGroup?type=in_group&feedId=9860930&view=all" target="_blank" class="btn"><i class="fa fa-share-alt "></i> BETA Yammer Channel</a>
			</section>
			<section class="col-md-6">
				<h3><strong>ABAP</strong>Analyzer</h3>
				<address>
					<strong>ABAP</strong>Analyzer es un proyecto creado por <a href="https://github.com/tianmarin" target="_blank">@cmarin</a> y distribuido bajo licencia <a href="https://html5up.net/license">Creative Commons Attribution</a><br>
				</address>
			</section>
			<p class="text-center ">
				<small>
				&copy; <strong>ABAP</strong>Analyzer. Diseñado y programado con &lt;3 por: <a href="https://github.com/tianmarin">@cmarin</a>.
				</small>
			</p>
		</div>		
	</div>
	<section id="sdfmon-setup" class="hidden">
		<a href="#system-list">Volver</a>
		<div class="sdfmon-setup-calendar"></div>
		<div class="sdfmon-setup-status">
			<div></div>
		</div>
	</section>
	<section id="sdfmon-instructions" class="hidden">
		<header class="container">
			<h1 class="text-center animated fadeInUp"><strong>ABAP</strong>Analyzer</h1>
			<p class="text-center lead animated fadeInLeft">Una nueva herramienta de an&aacute;lisis y revisión de sistemas SAP NW AS ABAP.</p>
		</header>
		<div class="container">
			<section class="">
				<h2 class="">Snapshot Monitoring <small>(/sdf/mon)</small></h2>
				<p>El Snapshot Monitoring permite almacenar diferentes indicadores de performance mediante muestras específicas (snapshots) en los sistemas ABAP.Es posible almacenar unformación de CPU, memoria, procesos, SQL, enqueues de aplicaciones, etc. También, se puede especificar la frecuencia y lsa ventanas de tiempo en el cual las muestras (snapshots) pueden ser tomadas. Basado en esos <em>snapshots</em> uno puede realizar an&aacute;lisis de de performance de programas, aplicaciones y sistemas para identificar &aacute;reas de oportunidad en diferentes configuraciones.</p>
				<p>La información que actualmente se almacena en el sistema <strong>ABAP</strong>ANALYZER es:</p>
				<ul>
					<li><code>Number of Active Work Processes</code></li>
					<li><code>Number of Active Dialog Work Processes</code></li>
					<li><code>Number of available WPs for RFC</code></li>
					<li><code>CPU Utilization (User)</code></li>
					<li><code>CPU Utilization (System)</code></li>
					<li><code>CPU Utilization (Idle)</code></li>
					<li><code>Available CPUs</code></li>
					<li><code>Paging In (kB/s)</code></li>
					<li><code>Paging Out (kB/s)</code></li>
					<li><code>Free Memory in KB</code></li>
					<li><code>Allocated Extended Memory in MB</code></li>
					<li><code>Attached Extended Memory in MB</code></li>
					<li><code>Ext. Mem. global</code></li>
					<li><code>Heap Memory in MB</code></li>
					<li><code>Priv Modes</code></li>
					<li><code>Paging Memory (KB)</code></li>
					<li><code>Roll Memory (KB)</code></li>
					<li><code>Dialog Queue Length</code></li>
					<li><code>Update Queue Length</code></li>
					<li><code>Enqueue Queue Length</code></li>
					<li><code>Number of logins</code></li>
					<li><code>Number of Sessions</code></li>
				</ul>
			</section>
			<section>
				<h3>1.- Pasos para configurar la recolecci&oacute;n de informaci&oacute;n</h3>
				<ol>
					<li>Ejecutar la transacci&oacute;n/reporte <code>/SDF/MON</code> (la transacci&oacute;n y el reporte tienen el mismo nombre).</li>
					<li>Seleccionar el bot&oacute;n <code>Schedule New Monitoring</code></li>
					<li>Seleccionar el bot&oacute;n <code>Schedule Daily Monitoring</code></li>
					<li>Los siguientes valores deben estar configurados:<br><img class="center-block" src="<?php echo plugins_url( 'img/instructions/sdfmon-instructions-001.png', __FILE__ );?>" alt=""/></li>
				</ol>
			</section>
			<section>
				<h3>2.- Como obtener las capturas de informaci&oacute;n</h3>
				<ol>
					<li>Ejecutar la transacción/reporte <code>/SDF/MON</code> (la transacci&oacute;n y el reporte tienen el mismo nombre).</li>
					<li>Seguir el men&uacute; <code>Program -> Execute</code> (esto es solo para listar los análisis almacenados en el sistema).</li>
					<li>Seleccionar el d&iacute;a que se desea obtener (se debe exportar cada an&aacute;lisis por d&iacute;a).</li>
					<li>Seleccionar el bot&oacute;n <code>Local File...</code>.</li>
					<li>Es importante guardar el archivo de salida con un identificador de fecha (para que no te confundas) y el formato debe ser <code>unconverted</code></li>
				</ol>
			</section>
			<section>
				<h3>3.- Como cargar la información a <strong>ABAP</strong>Analyzer</h3>
				<ol>
					<li>Con mucho cuidado</li>
				</ol>
			</section>
			<section>
				<p>Por ahora no es mucha información, lo sé. Pero eventualmente podremos poner un video que explique como hacerlo.</p>
			</section>
		</div>
	</section>
	<section id="os-setup" class="hidden">
		<a href="#">Volver</a>
	</section>
	<section id="system-list" class="hidden">
		<nav>
			<a href="#intro" class="aaBackButton"><i class="fa fa-arrow-left" aria-hidden="true"></i></a>
		</nav>
		<header>
			<h1 class="text-center">Sistemas</h1>
		</header>
		<article>
			<div class="btn-group btn-group-justified" role="group" aria-label="...">
				<a href="#new-system" class="btn btn-primary" id="new-system-button"><i class="fa fa-plus" aria-hidden="true"></i> Agregar Sistema</a>
			</div>
			<br/>
		</article>
		<article>
			<ul class="list-group">
			</ul>
		</article>
	</section>
	<section id="new-system" class="hidden">
		<nav>
			<a href="#system-list" class="aaBackButton"><i class="fa fa-arrow-left" aria-hidden="true"></i></a>
		</nav>
		<?php
			global $SYSTEM;
			echo $SYSTEM->fe_system_show_form('add');
		?>
	</section>
	<section id="system-info" class="hidden">
		<nav>
			<a href="#system-list" class="aaBackButton"><i class="fa fa-arrow-left" aria-hidden="true"></i></a>
		</nav>
	</section>
	<section id="load-sdfmon" class="hidden">
		<nav>
			<a href="#system-list" class="aaBackButton"><i class="fa fa-arrow-left" aria-hidden="true"></i></a>
		</nav>		
	</section>
	<section id="report-preview" class="hidden">
		<a href="#" class="aa-export">Exportar</a>
		<article>
			<header><h1>Introducción</h1></header>
			<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras condimentum massa massa, vitae mollis dolor sodales at. Donec sapien sapien, congue eget metus id, ornare blandit justo. Aenean volutpat placerat aliquet. Etiam commodo lacus in velit accumsan, sed ornare ipsum tincidunt. Proin purus orci, molestie non bibendum eget, sollicitudin a nisi. Etiam sed sem urna. Curabitur consequat porttitor vestibulum. Suspendisse varius eu elit sed bibendum.</p>
			<p>Etiam cursus sagittis tincidunt. Donec suscipit pharetra est. Mauris fringilla tortor ac nibh imperdiet porta. Nunc sit amet dolor egestas, elementum nibh in, posuere enim. Vestibulum lectus justo, suscipit sed placerat vitae, malesuada nec nunc. Praesent imperdiet ante nisi, eu iaculis ante dignissim sit amet. Integer sed euismod eros. Vivamus fringilla, diam at eleifend laoreet, augue mauris consequat massa, ac rutrum elit sem a felis.</p>
		</article>
		<!-- Cada gráfico llama a una función ajax independiente y asíncrona -->
	</section>
	<section id="system-collab" class="hidden">Modificar colaboradores</section>
    <?php wp_footer(); ?>
</body>
</html>

















