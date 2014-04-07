<?php

class teamObj{

      private $data;
      
      /* The constructor */
      public function __construct($par){
	    if(is_array($par))
		  $this->data = $par;
      }
      
      public function __toString(){
	    
	    $return .= '<li id="team-'.$this->data['id'].'" class="teamListElement">';
	    
	    $return .= '<img class="deleteTeam" width="15px" src="img/remove.png">';
	    $return .= '<img class="editTeam" width="15px" src="img/edit.png">';
	    
	    $return .= ' <span id="teamName-'.$this->data['id'].'">'.$this->data['name']."</span>";
	    
	    $return .= '</li>';
	    
	    return $return;
      
      }

}

?>