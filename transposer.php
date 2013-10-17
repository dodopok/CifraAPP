<?php
//require_once("dompdf/dompdf_config.inc.php");
 
class Transposer
{
	public $html;
	private $type = 'sharps';
	private $notes = array(
		'scale' => array(
		  'C'  => 1,
		  'C#' => 2,
		  'Db' => 2,
		  'D'  => 3,
		  'D#' => 4,
		  'Eb' => 4,
		  'E'  => 5,
		  'Fb' => 5,
		  'F'  => 6,
		  'F#' => 7,
		  'Gb' => 7,
		  'G'  => 8,
		  'G#' => 9,
		  'Ab' => 9,
		  'A'  => 10,
		  'A#' => 11,
		  'Bb' => 11,
		  'B'  => 12,
		  'Cb' => 12
		),
      'flats'  => array(1 => 'C', 'Db', 'D', 'Eb', 'E', 'F', 'Gb', 'G', 'Ab', 'A', 'Bb', 'B'),
      'sharps' => array(1 => 'C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#', 'A', 'A#', 'B')
   );
   private $search = '`([ABCDEFG][b#]?(?=\s(?![a-zH-Z])|(?=(2|5|6|7|9|11|13|6\/9|7\-5|7\-9|7\#5|7\#9|7‌​\+5|7\+9|7b5|7b9|7sus2|7sus4|add2|add4|add9|aug|dim|dim7|m\|maj7|m6|m7|m7b5|m9|m1‌​1|m13|maj7|maj9|maj11|maj13|mb5|m|sus|sus2|sus4|\))(?=(\s|\/)))|(?=(\/|\.|-|\(|\)))))`';
   private $search2 = '`([ABCDEFG][b#]?[m]?[\(]?(2|5|6|7|9|11|13|6\/9|7\-5|7\-9|7\#5|7\#9|7\+5|7\+9|7b5|7b9|7sus2|7sus4|add2|add4|add9|aug|dim|dim7|m\|maj7|m6|m7|m7b5|m9|m11|m13|maj7|maj9|maj11|maj13|mb5|m|sus|sus2|sus4)?(\))?)(?=\s|\.|\)|-|\/)`';   
   private $song;
   private $steps;
   private $formattedChords = array(); 
   private $replacementChords = array();
	
	public function __construct($song_,$steps_) {
		$this->song = $song_;
		$this->steps = $steps_;		  
		preg_match_all($this->search, $this->song, $song_chords); 
		//print_r($u = array_unique($song_chords[0]))."\n";
		$u = array_unique($song_chords[0]);
		foreach ($u as $chord){
			if (strlen($chord) > 1  && ($chord{1} == "b" || $chord{1} == "#"))
				array_push($this->formattedChords,substr($chord,0, 2));
			else
				array_push($this->formattedChords,substr($chord,0, 1));
		}
		//print_r($this->formattedChords)."\n";
		$this->song = preg_replace($this->search,'|$1|',$this->song);
		foreach($this->formattedChords as $note) {
			$len = strlen($note);
			$len = $len - 1;
			switch ($note{$len}) {
				case "b":			  
					$this->transpose($note,'flats',$this->steps);
					break;			
				case "#":
					$this->transpose($note,'sharps',$this->steps);
					break;
				default:
					$this->transpose($note,'sharps',$this->steps);
			}
		}
		foreach($this->formattedChords as &$note){
			$note = "/\|".$note."\|/";
		}
		$this->song = preg_replace($this->formattedChords, $this->replacementChords, $this->song);
		 $html= preg_replace($this->search2,'<b>$1</b>',$this->song);
		 $old = array("<pre>", "\r", "\n", "</pre>");
		  $new = array("<pre><span>", "", "</span>\n<span>", "</span></pre>");


		 $html ='<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><html><body><pre>'.$html.'</pre></body></html>';

		$html = str_replace($old, $new, $html);
		$html = str_replace("<span></span>","<span> </span>",$html);
		$this->html = $html;
	}
   
	public function transpose($note,$types,$steps){
		
		if(isset($this->notes['scale'][$note]))
		{
			$ix = $this->notes['scale'][$note];
		}
		else 
		{
			user_error("Invalid note '$note'");
			return false;
		}
		$ixNew = $ix + $steps;
		if(!isset($this->notes[$types][$ixNew])) {
			$ixNew += ($ixNew > 0) ? -12 : 12;
			if(!isset($this->notes[$types][$ixNew]))
				throw new Exception("My math skills suck! $note : $steps : $ix : $ixNew");
		}
		array_push($this->replacementChords,$this->notes[$types][$ixNew]);
		//echo "/|".$note."|/";	
	}
}
?>