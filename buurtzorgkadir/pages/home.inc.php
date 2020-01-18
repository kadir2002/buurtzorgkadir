<?php
	class HomePage extends Core implements iPage {
		public function getHtml() {
			return "<h1>Homepage content page</h1>";
		}
	}