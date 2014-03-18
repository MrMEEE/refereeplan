<?php

class teamObj{

      private $data;
      
      /* The constructor */
      public function __construct($par){
	    if(is_array($par))
		  $this->data = $par;
      }
      
      public function __toString(){
      
      
      }

}

?>