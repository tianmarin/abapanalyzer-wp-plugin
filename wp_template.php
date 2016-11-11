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
						<li name="intro-osmon"><a href="#intro-osmon">OS Monitoring</a></li>
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
						<p>No tengo buena memoria.</p>
					</li>
					<li class="col-xs-max col-sm-4">
						<i class="style3 fa fa-diamond fa-5x fa-fw"></i>
						<h3>Dolor nullam</h3>
						<p>Sed lorem amet ipsum dolor et amet nullam consequat a feugiat consequat tempus veroeros sed consequat.</p>
					</li>
				</ul>
				<div class="more text-center clearfix">
					<button class="text-center btn btn-default">Aprender m&aacute;s</button>
				</div>
			</section>
			<section id="intro-osmon" class="">
				<h2>OS Monitoring <small>(st07n | os06)</small></h2>
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras condimentum massa massa, vitae mollis dolor sodales at. Donec sapien sapien, congue eget metus id, ornare blandit justo. Aenean volutpat placerat aliquet. Etiam commodo lacus in velit accumsan, sed ornare ipsum tincidunt. Proin purus orci, molestie non bibendum eget, sollicitudin a nisi. Etiam sed sem urna. Curabitur consequat porttitor vestibulum. Suspendisse varius eu elit sed bibendum.</p>
				<p>Etiam cursus sagittis tincidunt. Donec suscipit pharetra est. Mauris fringilla tortor ac nibh imperdiet porta. Nunc sit amet dolor egestas, elementum nibh in, posuere enim. Vestibulum lectus justo, suscipit sed placerat vitae, malesuada nec nunc. Praesent imperdiet ante nisi, eu iaculis ante dignissim sit amet. Integer sed euismod eros. Vivamus fringilla, diam at eleifend laoreet, augue mauris consequat massa, ac rutrum elit sem a felis.</p>
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
				<a href="https://www.yammer.com/noviscorp.com/#/threads/inGroup?type=in_group&feedId=8032595&view=all" target="_blank" class="btn"><i class="fa fa-share-alt "></i> Yammer Channel</a>
			</section>
			<section class="col-md-6">
				<h3><strong>ABAP</strong>Analyzer</h3>
				<address>
					<strong>ABAP</strong>Analyzer es un proyecto creado por <a href="https://github.com/tianmarin">@cmarin</a> y distribuido bajo licencia <a href="https://html5up.net/license">Creative Commons Attribution</a><br>
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
		<a href="#">Volver</a>
		<div class="sdfmon-setup-calendar"></div>
		<div class="sdfmon-setup-status">
			<div></div>
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
			<ul class="list-group">
			</ul>
		</article>
	</section>
	<section id="system-info" class="hidden">
		<nav>
			<a href="#system-list" class="aaBackButton"><i class="fa fa-arrow-left" aria-hidden="true"></i></a>
		</nav>
<!--		<header class="col-xs-max col-sm-max col-md-4 col-lg-4">
			<h1 class="text-uppercase">{{system.sid}}</h1>
			<p class="lead">{{analysis.shortname}}</p>
			<h2>{{Colaboradores}}</h2>
			<p>{{system.owner}}</p>
			<h3>{{system.collaborators}}</h3>
			<ul>
				{{#each this}}
				<li><p>{{system.collab}}</p></li>
				{{/each}}
			</ul>
			<h3>{{system.instances}}</h3>
			<ul>
				 {{#each this}}
				<li><p>{{system.instance.name}}</p></li>
				{{/each}}
			</ul>
			<button class="btn btn-default">Editar</button>
			<button class="btn btn-danger">Eliminar</button>
		</header>
		<section  class="col-xs-max col-sm-6 col-md-4 col-lg-4">
			<h2>{{system.datasources}}</h2>
			<div class="panel panel-default">
				<div class="panel-heading">{{SDFMON}}</div>
				<div class="panel-body">{{sdfmon calendar}}</div>
				<div class="panel-body">{{sdfmon.oading.status}}</div>
			</div>
			<div class="panel panel-default">
				<div class="panel-heading">{{Tune Summary}}</div>
				<div class="panel-body">{{tune calendar}}</div>
				<div class="panel-body">{{tune.oading.status}}</div>
			</div>
		</section>
		<section  class="col-xs-max col-sm-6 col-md-4 col-lg-4">
			<h2> {{system.reports}} </h2>
			<button class="btn btn-default">+</button>
			{{#each this}}
			<h3> {{report.type}} </h3>
			<ul class="list-group">
				{{#each this}}
				<li class="list-group-item">
					<h3 class="list-group-item-heading">{{report.shorttitle}}</h3>
					<div class="list-group-item-text">
						<p>{{report.startdate}}</p>
						<p>{{report.enddate}}</p>
					</div>
				</li>
				{{/each}}
			</ul>
			{{/each}}
		</section>
		-->
	</section>
	<section id="edit-system" class="hidden">
		<h1>Editar Sistema <small>(solo owner)</small></h1>
		<form class="form-horizontal">
			<h2>Detalles del Sistema</h2>
			<div class="form-group">
				<label for="system-sid" class="col-sm-2 control-label">Identificador de Sistema</label>
				<div class="col-sm-10">
					<p class="form-control-static">SID</p>
					<p class="help-block">El identificador del sistema es utilizado en los gráficos y validado contra archivos de configuración. Si necesita modificar este valor contacte al administrador del sistema.</p>
				</div>
			</div>
			<div class="form-group">
				<label for="system-shortname" class="col-sm-2 control-label">Nombre Corto</label>
				<div class="col-sm-10">
					<input type="text" class="form-control" id="system-shortname" placeholder="Nombre Corto">
					<p class="help-block">El nombre corto permite identificar el sistema con una personalización diferente al SID. Por ejemplo, para sistemas CLON.</p>
				</div>
			</div>
			<h2>Colaboradores del sistema</h2>
			<div class="form-group">
				<label for="system-collab" class="col-sm-2 control-label">Permisos del Sistema</label>
				<div class="col-sm-10">
					<select class="form-control" id="system-collab">
						<option>Solo yo</option>
						<option>Algunos</option>
						<option>Todos</option>
					</select>
					<p class="help-block">Esto aplica exclusivamente para la modificaci&oacute;n de informaci&oacute;n t&eacute;cnica. La modificaci&oacute;n de las propiedades del sistema s&acute;olo pueden ser modificadas por el due&ntilde;o.</p>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label">Usuarios colaboradores</label>
				<div class="col-sm-10">
					<div class="panel panel-default">
						<div class="panel-heading">Usuarios colaboradores</div>
						<div class="list-group">
							<a href="#" class="list-group-item list-group-item-success">Usuarios colaboradores <span class="badge bg-danger">+</span></a>
							<a href="#" class="list-group-item" data-system-collab-id="9">Cristian Marín<span class="badge bg-danger">&times;</span></a>
						</div>								
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm-offset-2 col-sm-10">
					<button type="submit" class="btn btn-default">Cancelar</button>
					<button type="submit" class="btn btn-success">Guardar</button>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm-offset-2 col-sm-10">
					<button type="submit" class="btn btn-danger">Eliminar</button>
				</div>
			</div>
		</form>			
	
	</section>
	<section id="edit-report" class="hidden">
		<header class="col-xs-max col-sm-4 col-md-4 col-lg-4">
			<h1 class="text-uppercase">{{report.name}}</h1>
			<p class="lead">{{report.system.sid}}</p>
			<h2>{{Colaboradores}}</h2>
			<p>{{report.owner}}</p>
			<h3>{{report.collaborators}}</h3>
			<ul>
				{{#each this}}
				<li><p>{{report.collab}}</p></li>
				{{/each}}
			</ul>
			<div class="btn-group">
				<button type="button" class="btn btn-default">Editar</button>
				<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					<span class="caret"></span>
					<span class="sr-only">Opciones</span>
				</button>
				<ul class="dropdown-menu">
					<li><a href="#">Editar</a></li>
					<li><a href="#">Previsualizar</a></li>
					<li role="separator" class="divider"></li>
					<li><a href="#">Eliminar</a></li>
				</ul>
			</div>
			<span role="separator" class="divider"></span>
			<button class="btn btn-default">Editar</button>
			<button class="btn btn-danger">Eliminar</button>
		</header>
		<section  class="col-xs-max col-sm-8 col-md-8 col-lg-8">
			<h2>{{report}}</h2>
			<div class="panel panel-default">
				<div class="panel-heading">{{Fechas}}</div>
				<div class="panel-body">
					<div class="media">
						<div class="media-body">
							<p>{{report.startdate}}</p>
							<p>{{report.enddate}}</p>
						</div>
						<div class="media-right media-top">
							<span class="media-object">{{chart}}</span>
						</div>
					</div>
				</div>
			</div>
			<div class="panel panel-default">
				<div class="panel-heading">Tabla de Contenidos</div>
				<div class="panel-body">
					<ol>
						<li>Section 1
							<ol>
								<li>Section 1.1</li>
								<li>Section 1.2</li>
								<li>Section 1.3
									<ol>
										<li>Section 1.3.1</li>
										<li>Section 1.3.2</li>
									</ol>
								</li>
							</ol>
						</li>
						<li>Section 2</li>
						<li>Section 3
							<ol>
								<li>Section 3.1</li>
								<li>Section 3.2</li>
							</ol>
						</li>
						<li>Section 4</li>
						<li>Section 5</li>
						<li>Section 6</li>
					</ol>
				</div>
			</div>
		</section>
	
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
	<section id="edit-report" class="hidden">
		<h1>Editar Reporte <small>(solo owner)</small></h1>
		<form class="form-horizontal">
			<h2>Detalles del Reporte</h2>
			<div class="form-group">
				<label for="report-shortname" class="col-sm-2 control-label">Nombre Corto</label>
				<div class="col-sm-10">
					<input type="text" class="form-control" id="report-shortname" placeholder="Nombre Corto">
					<p class="help-block">El nombre corto permite identificar el reporte de modo sencillo y &aacute;gil.</p>
				</div>
			</div>
			<div class="form-group">
				<label for="report-startdate" class="col-sm-2 control-label">Período de Análisis</label>
				<div class="col-sm-10">
					<div class="col-sm-6">
						<input type="date" class="form-control" id="report-startdate" placeholder="Fecha Inicio">
					</div>
					<div class="col-sm-6">
						<input type="date" class="form-control" id="report-enddate" placeholder="Fecha Final">
					</div>						
					<p class="help-block">El reporte puede ser acotado a las fechas indicadas. Considerando que un sistema puede tener un alto volumen de informaci&oacuten, este campo <mark>obligatorio</mark>, permite definir las fechas de an&aacute;lisis.</p>
				</div>
			</div>
			<div class="form-group">
				<label for="report-threshold" class="col-sm-2 control-label">Umbral de recomendaciones</label>
				<div class="col-sm-10">
					<div class="input-group">
						<input type="number" class="form-control" id="report-threshold" placeholder="Umbral" min="50" max="100">
						<span class="input-group-addon" id="basic-addon2">%</span>
					</div>
					<p class="help-block">Las recomendaciones de este reporte, consideran reservar un porcentaje m&aacute;ximo de la utilizaci&oacute;n de los recursos. Regularmente, se recomienda un 75%-80%.</p>
				</div>
			</div>
			<div class="form-group">
				<label for="report-threshold" class="col-sm-2 control-label">Instancias SAP</label>
				<div class="col-sm-10">
					<div class="list-group">
						{{#each this}}
						<input type="checkbox" id="inst001" checked class="aa-check-button">
						<label for="inst001" class="list-group-item btn-default">{{hostname_SID_XX}}<span class="pull-right"></span></label>
						{{/each}}
					</div>
					<p class="help-block">Las instancias que no est&eacute;n seleccionadas ser&aacute;n exclu&iacute;das del an&aacute;lisis.</p>
				</div>
			</div>
			<div class="form-group">
				<label for="report-threshold" class="col-sm-2 control-label">Permitir nuevas instancias</label>
				<div class="col-sm-10">
					<div class="list-group">
						<input type="checkbox" id="report-additional-instances" checked class="aa-check-button">
						<label for="report-additional-instances" class="list-group-item btn-default">Permitir que los reportes recomienden la instalación de nuevas instancias SAP.<span class="pull-right"></span></label>
					</div>
					<p class="help-block">Si el volumen de transacciones en el sistema lo requeire, es posible que el ajuste de parámetros recomiende instancias SAP demasiado grandes.</p>
				</div>
			</div>
			<h2>Colaboradores del reporte</h2>
			<div class="form-group">
				<label for="system-collab" class="col-sm-2 control-label">Permisos del Reporte</label>
				<div class="col-sm-10">
					<select class="form-control" id="system-collab">
						<option>Solo yo</option>
						<option>Algunos</option>
						<option>Todos</option>
					</select>
					<p class="help-block">Esto aplica exclusivamente para la modificaci&oacute;n de informaci&oacute;n t&eacute;cnica. La modificaci&oacute;n de las propiedades del sistema s&acute;olo pueden ser modificadas por el due&ntilde;o.</p>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-2 control-label">Usuarios colaboradores</label>
				<div class="col-sm-10">
					<div class="panel panel-default">
						<div class="panel-heading">Usuarios colaboradores</div>
						<div class="list-group">
							<a href="#" class="list-group-item list-group-item-success">Usuarios colaboradores <span class="badge bg-danger">+</span></a>
							<a href="#" class="list-group-item" data-report-collab-id="9">Cristian Marín<span class="badge bg-danger">&times;</span></a>
						</div>								
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm-offset-2 col-sm-10">
					<button type="submit" class="btn btn-default">Cancelar</button>
					<button type="submit" class="btn btn-success">Guardar</button>
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm-offset-2 col-sm-10">
					<button type="submit" class="btn btn-danger">Eliminar</button>
				</div>
			</div>
		</form>			
	
	</section>
    <?php wp_footer(); ?>
</body>
</html>

















