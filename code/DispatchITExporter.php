<?php
class DispatchITExporter extends Controller{

	static $url_segment = 'dispatchitexporter';

	static $allowed_actions_disabled = array(
		'index' => 'ADMIN',
		'export' => 'ADMIN',
	);

	function init(){
		parent::init();
		if(!(Director::isDev() || Director::is_cli() || Permission::check("ADMIN")))
			return Security::permissionFailure($this);
	}
	
	function Link($action = null,$id = null){
		return Controller::join_links(self::$url_segment,$action,$id);
	}
	
	function index(){
		return $this->export();
	}

	function export(){
		$states = "'".implode("','",Order::$placed_status)."'";
		$filter = "\"SentToDispatchIT\" = FALSE AND \"Order\".\"Status\" IN($states) AND \"Address\".\"Country\" = 'NZ'";
		$sort = "\"Placed\" ASC, \"Created\" ASC";
		$join = "INNER JOIN \"Address\" ON \"Order\".\"ShippingAddressID\" = \"Address\".\"ID\"";
		$orders = DataObject::get('Order',$filter,"",$join);
		$output = "";
		if($orders){
			foreach($orders as $order){
				$address = $order->getShippingAddress();
				$name = $address->Company; //TODO: company
				if(!$name || $name == ""){
					$name = $order->Name;
				}
				$line = array(
					$order->ID,			//ConsignmentNumber	char(12)	Yes	Tracking number.  Must be unique.
					$order->MemberID, //CustomerID			char(20)	Blank if ommitted
					$name,				//CompanyName			char(40)	Yes
					$address->Address,	//Address1			char(40)	Yes
					$address->AddressLine2,	//Address2		char(40)	Yes
					$address->Suburb,	//Address3				char(40)	Blank if ommitted
					$address->State,	//Address4				char(40)	Blank if ommitted
					$address->City,		//Address5			char(40)	Must be valid suburb/city/town.
					"",					//CustOrderRef			char(30)	Blank if ommitted
					"0",					//Carrier				smallint	Yes	0 = NZ Couriers
					"1",					//CostCentre			smallint	Yes	1 = Primary cost centre.
					"",					//PhoneSTD				char(4)	Blank if ommitted
					$address->Phone,	//PhoneNumber			char(20)	Blank if ommitted
					"",					//FaxSTD					char(4)	Blank if ommitted
					$order->Fax,		//FaxNumber				char(20)	Blank if ommitted
					$order->Email,		//EmailAddress			char(255)Blank if ommitted
					"1",					//EmailAddressSend	bit		TRUE or FALSE
					"",					//EmailAddress2		char(255)Blank if ommitted
					"0",					//EmailAddress2Send	bit		TRUE or FALSE
					"",					//EmailAddress3		char(255)Blank if ommitted
					"0"					//EmailAddress3Send	bit		TRUE or FALSE;
				);
				$output .= implode("\t",$line)."\n";
				$order->SentToDispatchIT = true;
				$order->write();
			}
			//store output in a local file, then output the contents, or a link to the file
				//name format: DDcxxxxxxx.TXT
			$filename = "DDc".uniqid()."_".date('YmdHis').".txt";
			$exportpath = ASSETS_PATH."/_dispatchitexports";
			if(!is_dir($exportpath)){
				mkdir($exportpath);
			}
			if(file_put_contents($exportpath."/$filename",$output) === false){
				$output = "failed to save file";
			}
		}else{
			$output = "no new orders";
		}
		header('Content-type: text/plain');
		echo $output;
		die();
	}

}