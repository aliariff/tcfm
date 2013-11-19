<?php
	class TemplateData {
		public $scripts;
		public $template_part;
		public $sidebar;
		public $page_title;
		public $page_layout;
		public $data;
		
		public function __construct () {
			$this->scripts = array();
			$this->template_part = '';
			$this->sidebar = '';
			$this->page_title = '';
			$this->page_layout = '';
			$this->data = new stdClass();
		}
	}
