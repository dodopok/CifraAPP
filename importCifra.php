<?php
	include 'include/database.php';
	function retiraAcentos($texto) 
	{ 
		$array1 = array( "á", "à", "â", "ã", "ä", "é", "è", "ê", "ë", "í", "ì", "î", "ï", "ó", "ò", "ô", "õ", "ö", "ú", "ù", "û", "ü", "ç" 
		, "Á", "À", "Â", "Ã", "Ä", "É", "È", "Ê", "Ë", "Í", "Ì", "Î", "Ï", "Ó", "Ò", "Ô", "Õ", "Ö", "Ú", "Ù", "Û", "Ü", "Ç" ); 
		$array2 = array( "a", "a", "a", "a", "a", "e", "e", "e", "e", "i", "i", "i", "i", "o", "o", "o", "o", "o", "u", "u", "u", "u", "c" 
		, "A", "A", "A", "A", "A", "E", "E", "E", "E", "I", "I", "I", "I", "O", "O", "O", "O", "O", "U", "U", "U", "U", "C" ); 
		return str_replace( $array1, $array2, $texto); 
	}

	if(!empty($_POST['musicas'])){
		$musicas = explode("\n", $_POST['musicas']);
		foreach ($musicas as $musica) {
			$musica = explode(' - ', $musica);
			$artist = str_replace("\r", '', $musica[1]);
			$title = $musica[0];
			$artist_cc = str_replace(' ', '-', strtolower(retiraAcentos($artist)));
			$title_cc = str_replace(' ', '-', strtolower(retiraAcentos($title)));

			$chord = file_get_contents('http://www.cifraclub.com.br/'.$artist_cc.'/'.$title_cc.'/');
			$dom = new DOMDocument();
			@$dom->loadHTML($chord);
			$xpath = new DOMXpath($dom);			
			$result = $xpath->query('//div[@id="cifra_cnt"]');
			if (!empty($result->item(0)->nodeValue)) {
			    $chord = utf8_decode($result->item(0)->nodeValue);
			    $pos = strpos($chord, 'Tom: ');
			    $tom = trim(substr($chord, $pos+4, 5));
			    $chord = '<pre data-key="'.$tom.'">'.str_replace('									', '', $chord).'</pre>';
			    $count = mysql_fetch_array(mysql_query('SELECT COUNT(*) AS count FROM chords WHERE artist_cc = "'.mysql_escape_string($artist_cc).'" AND title_cc="'.mysql_escape_string($title_cc).'"'));
			    if($count['count'] == 0){
			    	mysql_query("INSERT INTO chords (artist, title, artist_cc, title_cc, chord) VALUES ('".mysql_escape_string(ucwords(utf8_decode($artist)))."', '".mysql_escape_string(ucwords(utf8_decode($title)))."', '".mysql_escape_string($artist_cc)."', '".mysql_escape_string($title_cc)."', '".mysql_escape_string($chord)."')") or die(mysql_error());
			    }
			}
		}
	}else{
		die('post');
	}
?>