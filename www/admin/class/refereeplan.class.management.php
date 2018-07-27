<?php

class clubObj{

      private $data;
      
      /* The constructor */
      public function __construct($par){
	    if(is_array($par))
		  $this->data = $par;
      }
      
      public function __toString(){
	    
	    $return = '<li id="club-'.$this->data['id'].'" class="clubListElement">';
	    
            if(mysqli_num_rows(ref_mysql_query("SELECT * FROM `config` WHERE `enabled`='1' AND `id`='".$this->data['id']."'")) != 0){  
               $return .= '<img class="activateClub" width="15px" src="img/add.png">';
            }else{
               $return .= '<img class="activateClub" width="15px" src="img/remove.png">';
	    }
	    $return .= ' <span id="clubName-'.$this->data['id'].'">'.$this->data['name']."</span>";
	    
	    $return .= '</li>';
	    
	    return $return;
      
      }

}

?>
