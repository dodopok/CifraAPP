<?php 
    include('include/database.php');
    $chords = mysql_query('SELECT id, artist, title FROM chords ORDER BY artist');
?>
<!DOCTYPE html> 
<html manifest="match.manifest">
<head> 
	<title>Cifra APP</title> 
    <meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />
    <link rel="apple-touch-icon" href="images/logo_cifra_app_128x128.png">
	<link rel="stylesheet" href="css/jquery.mobile.css" />
    <link rel="stylesheet" href="css/css.css" />
    <link rel="stylesheet" href="css/add2home.css">
    <link rel="stylesheet" href="css/jquery.transposer.css">
    <link rel="shortcut icon" href="favicon.ico">
    <script src="js/add2home.js"></script>
	<script src="js/jquery.js"></script>
	<script src="js/jquery.mobile.js"></script>
    <script src="js/jquery.transposer.js"></script>
    <script src="js/js.js"></script>
</head> 
<body> 

<div data-role="page" id="page">

    <div data-role="panel" id="left-panel" data-position="left" data-display="reveal">
        <ul id="cifras-listview" data-role="listview" data-autodividers="true" data-filter-placeholder="Buscar Cifras..." data-filter="true" data-inset="true">
            <?php while($chord = mysql_fetch_array($chords)) {?>
                <li data-id="<?php echo $chord['id']; ?>" artist="<?php echo utf8_encode($chord['artist']); ?>"><a href="#" onclick="loadCifra(<?php echo $chord['id'] ?>);"><?php echo utf8_encode($chord['title']); ?></a></li>
            <?php } ?>            
        </ul>
    </div><!-- /panel -->
    
    <div data-role="popup" id="popupImport" data-theme="a" class="ui-corner-all">
        <p>Cifra(s) importada(s) com sucesso! Agora esta(s) cifra(s) está(ão) disponível(is) para visualização!<p>
    </div>

    <div data-role="popup" id="popupFavorite" data-theme="a" class="ui-corner-all">
        <p>Cifra favoritada com sucesso! Agora esta cifra se mantém acessível neste aparelho mesmo quando off-line!<p>
    </div>    

    <div data-role="popup" id="popupRolagem">
        <form>
            <div style="padding:10px 20px;">
                <h3>Configurações de Rolagem</h3>

                <div class="flip-rolagem">
                    <select name="flip-1" onChange="scroll();" id="flip-1" data-role="slider">
                        <option value="off">Desativada</option>
                        <option value="on">Ativada</option>
                    </select> 
                </div>

                <input type="range" name="slider-1" onChange="velocidade = this.value;" data-highlight="true" id="slider-1" value="20" min="0" max="50" />
            </div>
        </form>
    </div>

    <div data-role="popup" id="popupConfigure">
        <div style="padding:10px 20px;">
            <h4>Tamanho da Fonte</h4>
            <div data-role="controlgroup" data-type="horizontal">
                <a href="javascript:;" data-iconpos="notext" data-role="button" data-icon="plus" onClick="aumentaFonte();">+</a>
                <a href="javascript:;" data-iconpos="notext" data-role="button" data-icon="minus" onClick="diminuiFonte();">_</a>
            </div>
        </div>
    </div>

    <div data-role="popup" id="popupSetlist">
        <ul id="cifras-setlist" data-role="listview" data-inset="true">
        </ul>
    </div>
    
    <div data-role="popup" id="popupAddCifra" data-theme="a" class="ui-corner-all">
        <form>
            <div style="padding:10px 20px;">
                <h3>Nova Cifra</h3>
                <input type="text" name="music" id="un" value="" placeholder="Música" data-theme="a" />
                <input type="text" name="artist" id="pw" value="" placeholder="Artista" data-theme="a" />                    
                <textarea name="chord" id="pw" placeholder="Cifra" data-theme="a" rows="10" cols="70"></textarea>
                <button type="submit" data-theme="b">Adicionar Cifra</button>
            </div>
        </form>
    </div>

    <div data-role="popup" id="popupImportCifra" data-theme="a" class="ui-corner-all">
        <form method="POST" action="importCifra.php" id="importCifra" data-ajax="false">
            <div style="padding:10px 20px;">
                <h3>Importar Cifra(s) - Obs.: Separe os artistas das músicas com " - " e as músicas por linha</h3>                    
                <textarea name="musicas" id="pw" placeholder="Música - Artista (separados por linha)" data-theme="a" rows="10" cols="70"></textarea>
                <button type="submit" data-theme="b">Importar Cifra(s)</button>
            </div>
        </form>
    </div>

	<div data-role="header">        
        <div class="ui-btn-left" data-role="controlgroup" data-type="horizontal">
            <a href="#left-panel" data-role="button" data-icon="bars" data-iconpos="notext">Menu</a>
            <a href="index.php" data-role="button" data-icon="home" data-iconpos="notext">Início</a>
            <a href="#popupAddCifra" data-rel="popup" data-position-to="window" data-iconpos="notext" data-role="button" data-icon="plus">Nova</a>
            <a href="#popupImportCifra" data-rel="popup" data-position-to="window" data-role="button" data-iconpos="notext" data-icon="gear">Configurações</a>
        </div>
		<h1>
            <span id="title">Cifra APP - Início</span>
        </h1>
        <div class="ui-btn-right" data-role="controlgroup" data-type="horizontal">
            <a href="#popupSetlist" data-rel="popup" data-position-to="origin" data-iconpos="notext" data-role="button" data-icon="star">Setlist</a>
            <a href="#popupRolagem" data-rel="popup" data-position-to="origin" data-role="button" data-icon="forward">Rolagem Automática</a>                       
        </div>
	</div><!-- /header -->   

	<div data-role="content" id="contentCA">	
		<div class="article" id="chordT">
    		<p><img src="images/musica.png" alt="Cifra APP"></p>

            <h2>Cifra APP</h2>

            <p>Uma aplicação para armazenar suas cifras, mesmo sem internet, com opções para alterar tom, gravar favoritas, salvar para acesso offline e muito mais.</p>

		</div><!-- /article -->	
	</div><!-- /content -->

</div><!-- /page -->

</body>
</html>