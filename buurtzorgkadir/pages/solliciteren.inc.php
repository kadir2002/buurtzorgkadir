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
					case "create"	: return $this->create(); break;
					case "read"		: return $this->read(); break;
					case "update"	: return $this->update();break;
				}
			} else { // no ACTION so normal page
				 // add "/add" button. This is ACTION button
				// first show button, then table
				$table 	= $this->getData();		// get users from database in tableform
				$button = $this->addButton("/create", "Toevoegen");	// add "/add" button. This is ACTION button
				// first show button, then table
				$html = $button . "<br />" . $table;

				return $html;
			}
			return "Vacature content page";
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
			$image = "<img src='".ICONS_PATH."noun_information user_24px.png' />";
			$table = "<table border='1'>";
				$table .= "	<th>Vacature ID</th>
							<th>Vacature Beschrijving</th>
							<th>Vacature Titel</th>
							<th>WTV ID</th>
							<th>Bekijk</th>
							<th>Solliciteer</th>";

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
								. "/read/" . $row["vac_id"] 	// add ACTION and PARAM to the link
								. ">$image</a></td>";		
								
								$table 	.= "<td><a href="
								. $url 							// current menu
								. "/create/" . $row["vac_tekst"] 	// add ACTION and PARAM to the link
								. ">$image</a></td>";	
						
				
						//create new link with parameter (== delete user)
							
						
					$table .= "</tr>";
					
				} // foreach
			$table .= "</table>";
			return $table;
		}
		private function create() {
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
						<label>Naam en Achternaam</label>
							<input type="text" name="vac_id" id="" value="" placeholder="Vacature ID" />

							<label>Adres</label>
							<input type="text" name="vac_tekst" id="" value="" placeholder="Description" />

							<label>Geboortedatum</label>
							<input type="text" name="vac_titel" id="" value="" placeholder="Vacature Titel" />

							<label>E-mail</label>
							<input type="text" name="wtv_id" id="" value="" placeholder="WTV ID" />


							<label></label>
							<!-- add hidden field for processing -->
							<input type="hidden" name="frmAddUser" value="frmAddUser" />
							<input type="submit" name="submit" value="Voeg toe" />
						</form>
				</fieldset>
HTML;
			return $html;
		} 
		// function
		private function processFormAddUser() {
			
			$naamid 			= $_POST['naamid'];
			// get transfered datafields from form "$this->addForm()"
	
			$adres 				= $_POST['adres'];
			$gebDatum			= $_POST['gebDatum'];
			$mail				= $_POST['mail'];
			// create insert query with all info above
			$sql = "INSERT
						INTO `tb_soll`
							(naamid, adres, gebDatum, mail)
								VALUES
									('$naamid', '$adres', '$gebDatum', '$mail')";

			Database::getData($sql);
			/*
				echo "<br />";
				echo $hash . "<br />";
				echo $uuid . "<br />";
				echo $hashDate . "<br />";
			*/
			return "Uw sollicitatie is verstuurd!";
		} //function

		//function
		// [C]rud action
		// based on sent form 'frmAddUser' fields

		// c[R]ud action
		private function read() {
			// get and present information from the user with uuid in PARAM
			$button = $this->addButton("/../..", "Terug");	
			// first show button, then table
			
			return $button ."<br> " . PARAM;
			
		} // function details

	

		//cru[D] action
		private function solliciteren() {
			// remove selected record based om uuid in PARAM
			$button = $this->addButton("/../..", "Terug");	// add "/add" button. This is ACTION button
			// first show button, then table

			return $button ."<br>Hier kunt u solliciteren " . PARAM;
		}

		
	}
	
