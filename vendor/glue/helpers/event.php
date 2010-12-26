<?php

/*
	Class: EventHelper
		Event helper class. Create and manage Events. (Thx yootheme)
*/
class EventHelper {

	/* events */
	protected static $_events = array();

	/*
		Function: bind
			Bind a function to an event.

		Parameters:
			$event - Event name
			$callback - Function callback

		Returns:
			Void
	*/
	public function bind($event, $callback) {
		
		if (!isset(self::$_events[$event])) {
			self::$_events[$event] = array();
		}
		
		self::$_events[$event][] = $callback;
	}

	/*
		Function: trigger
			Trigger Event

		Parameters:
			$event - Event name
			$parameters - Function arguments

		Returns:
			Void
 	*/
	public function trigger($event, $args = array()) {
		
		if (isset(self::$_events[$event])) {
			foreach (self::$_events[$event] as $callback) {
				call_user_func_array($callback, $args);
			}
		}

	}
	
}