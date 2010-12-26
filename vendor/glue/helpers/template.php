<?php

/*
	Class: TemplateHelper
		Utils helper class. Misc helper functions.
*/
class TemplateHelper {
    
	/* slots */
	protected $slots;
	
	/*
		Function: render
			Renders template.

		Parameters:
			$____template - template path
			$_____slots - array slots

		Returns:
			String
	*/
	public function render($____template, $_____slots = array()) {
		
		$this->slots = $_____slots;
		$____layout  = false;
		
		
		if (strpos($____template, ' with ') !== false ) {
			list($____template, $____layout) = explode(' with ', $____template, 2);
		}
		
		
        if (strpos($____template, ':') !== false && $____file = glue('path')->path($____template)) {
            $____template = $____file;
        }
		
		extract($_____slots);
		
		ob_start();
		include $____template;
		$output = ob_get_clean();
		
		
		if ($____layout) {
		
			if (strpos($____layout, ':') !== false && $____file = glue('path')->path($____layout)) {
				$____layout = $____file;
			}

			$content_for_layout = $output;
			
			ob_start();
			include $____layout;
			$output = ob_get_clean();
			
        }
		
		
		return $output;
	}
	
	/*
		Function: element
			Renders element.

		Parameters:
			$_____element - element path
			$_____slots - array slots

		Returns:
			String
	*/
	public function element($_____element, $_____slots = array()) {
		
        if (strpos($_____element, ':') !== false && $____file = glue('path')->path($_____element)) {
            $_____element = $____file;
        }
		
		extract($_____slots);
		
		ob_start();
		include $_____element;
		$output = ob_get_clean();
		
		return $output;
	}

}