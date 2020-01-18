<?php
/*************************************************************
	Pagebuilder framework application
	Learning application for VISTA AO JL2 P5
	Created 2019 by e.steens
*************************************************************/
/*
	Contains details of page information
	returns the built html
	Class Name convention: <pagename>Page
	Must contain iPage interface implementation ie getHtml()
	Called by content.inc.php
*/
class VacaturePage extends Core implements iPage{
		public function getHtml() {
			if(defined('ACTION')) {			// process the action obtained is existent
				switch(ACTION) {
					// get html for the required action
					case "solliciteren"	: return $this->solliciteren(); break;
					case "bekijken"		: return $this->bekijken(); break;
					case "update"		: return $this->update();
				}
			} elseif($_SESSION['user']['role'] == ROLE_LID) { 
				$table 	= $this->getData();		// get users from database in tableform
				$button = $this->addButton("/create", "Toevoegen");	// add "/add" button. This is ACTION button
				// first show button, then table
				$html = "<h1> Welkom " . $_SESSION['user']['username'] . " </h1>" . "<br/>" . $button . "<br/>" . $table;
				return $html;
			} else {
				$table 	= $this->getData();		// get users from database in tableform
				$html = "<h1> Welkom " . $_SESSION['user']['username'] . " </h1>" . "<br/>" . $table;
				return $html;
			}
			
		}

		

		// show button with the PAGE $p_sAction and the tekst $p_sActionText
		private function addButton($p_sAction, $p_sActionText) {
			// calculate url and trim all parameters [0..9]
            $url = rtrim($_SERVER['REQUEST_URI'],"/[0..9]");
			// create new link with PARAM for processing in new page request
			$url = $url . $p_sAction;
			$button = "<button onclick='location.href = \"$url\";'>$p_sActionText</button>";
			return $button;
		}
		private function getData(){
			// execute a query and return the result
			$sql='SELECT * FROM `tb_vacature`';
            $result = $this->createTable(Database::getData($sql));

			//TODO: generate JSON output like this for webservices in future
			/*
				$data = Database::getData($sql);
				$json = Database::jsonParse($data);
				$array = Database::jsonParse($json);

				echo "<br />result: ";  print_r(Database::getData($sql));
	            echo "<br /><br />json :" . $json;
	            echo "<br /><br />array :"; print_r($array);
			*/

			return $result;
		} // end function getData()


		private function createTable($p_aDbResult){ // create html table from dbase result
			if ($_SESSION['user']['role'] == ROLE_WTV) {
			$image = "<img src='".ICONS_PATH."noun_information user_24px.png' />";
			$table = "<table border='1'>";
				$table .= "	<th>Vacature ID</th>
							<th>Vacature Beschrijving</th>
							<th>Vacature Titel</th>
							<th>WTV ID</th>
							<th>Bekijken</th>
							<th>Solliciteren</th>";

				// now process every row in the $dbResult array and convert into table
				foreach ($p_aDbResult as $row){
					$table .= "<tr>";
						foreach ($row as $col) {
							$table .= "<td>" . $col . "</td>";
						}
	                    // calculate url and trim all parameters [0..9]
	                    $url = rtrim($_SERVER['REQUEST_URI'],"/[0..9]");
						// create new link with parameter (== edit user link!)
						$table 	.= "<td><a href="
								. $url 							// current menu
								. "/bekijken/" . $row["vac_id"] 	// add ACTION and PARAM to the link
								. ">$image</a></td>";		
								
								$table 	.= "<td><a href="
								. $url 							// current menu
								. "/solliciteren/" . $row["vac_tekst"] 	// add ACTION and PARAM to the link
								. ">$image</a></td>";	
						
				
						//create new link with parameter (== delete user)
							
						
					$table .= "</tr>";
					
				} // foreach
			$table .= "</table>";
			return $table;
		}else {
			$image = "<img src='".ICONS_PATH."noun_information user_24px.png' />";
			$table = "<table border='1'>";
				$table .= "	<th>Vacature ID</th>
							<th>Vacature Beschrijving</th>
							<th>Vacature Titel</th>
							<th>WTV ID</th>
							<th>Bekijken</th>";
							// now process every row in the $dbResult array and convert into table
				foreach ($p_aDbResult as $row){
					$table .= "<tr>";
						foreach ($row as $col) {
							$table .= "<td>" . $col . "</td>";
						}
	                    // calculate url and trim all parameters [0..9]
	                    $url = rtrim($_SERVER['REQUEST_URI'],"/[0..9]");
						// create new link with parameter (== edit user link!)
						$table 	.= "<td><a href="
								. $url 							// current menu
								. "/bekijken/" . $row["vac_id"] 	// add ACTION and PARAM to the link
								. ">$image</a></td>";		
											
						//create new link with parameter (== delete user)
					$table .= "</tr>";
					
				} // foreach
			$table .= "</table>";
			return $table;
			}
		}

		private function solliciteren() {
			// use variabel field  from form for processing -->
			if(isset($_POST['frmAddUser'])) { // in this case the form is returned
				return $this->processFormAddUser();
			} // ifisset
			else {								// in this case the form is made
				return $this->addForm();
			} //else
		}

		private function addForm() { // processed in $this->processFormAddUser()
			$url = rtrim($_SERVER['REQUEST_URI'],"/[0..9]"); 	// strip not required info
			// heredoc statement. Everything between 2 HTML labels is put into $html
			$html = <<<HTML
				<fieldset>
					<legend>Solliciteerformulier</legend>
						<form action="$url" enctype="multipart/formdata" method="post">
						<label>Vacature ID</label>
							<input type="text" name="vacid" id="vacid" value="" placeholder="Vac ID" />
							
							<label>Naam en Achternaam</label>
							<input type="text" name="naam" id="naamid" value="" placeholder="Voor en Achternaam" />

							<label>Adres</label>
							<input type="text" name="adres" id="adres" value="" placeholder="Adres" />

							<label>Geboortedatum</label>
							<input type="text" name="gebdatum" id="gebDatum" value="" placeholder="Geboortedatum" />

							<label>E-mail</label>
							<input type="text" name="mail" id="mail" value="" placeholder="E-mail" />

							<label>CV</label>
							<input type="file" name="Bestand" value="bestand">
							<label></label>
							<!-- add hidden field for processing -->
							<input type="hidden" name="frmAddUser" value="frmAddUser" />
							<input type="submit" id="submit" name="submit" value="Versturen">
							
						</form>
				</fieldset>
HTML;
			return $html;
		} 
		// function
		private function processFormAddUser() {
			$vacatureid		= $_POST["vacid"];
			$naamidd		= $this->createUuid(); // in code
			$test1			= $_POST["naam"];
			// get transfered datafields from form "$this->addForm()"
	
			$test2			= $_POST["adres"];
			$test3			= $_POST["gebdatum"];
			$test4			= $_POST["mail"];
			$status 		="1";
			$punten			="0";
		

	
			// create insert query with all info above
			$sql = "INSERT
						INTO `tb_soll`
						(vac_id, naamid, naam, adres, gebdatum, mail, status, punten)
								VALUES
									('$vacatureid', '$naamidd', '$test1', '$test2', '$test3', '$test4', '$status', $punten)";
			
			$result = Database::getData($sql);

			
		
			
			return "Uw sollicitatie is verstuurd!";
		} //function

		//function
		// [C]rud action
		// based on sent form 'frmAddUser' fields

		// c[R]ud action
		private function bekijken() {
			// get and present information from the user with uuid in PARAM
			$button = $this->addButton("history.go(-1)", "Terug");	
			// first show button, then table
			
			return $button ."<br> ";
			
		} // function details

	



		
	}
	