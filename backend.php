<?
plugins_register_backend($plugin,array("icon"=>"icon-article","sub"=>array(
  "positions"=>"Rechnungsposten",
  "invoices"=>"Rechnungen",
  "payments"=>"Zahlungsvorgaenge",
)));
require("class.Position.php");
require("class.Invoice.php");
require("class.Payment.php");

function Inv_showBillList() {
  $list=Inv_Invoice::getByFilter("order by id desc");
  DBObj_Interface_JSON::listView("Inv_Invoice",$list);
}
plugins_register_backend_handler($plugin,"transactions","showbilllist","Inv_showBillList");
