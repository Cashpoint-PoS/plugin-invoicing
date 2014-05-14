<?
//Invoice positions
class Inv_Payment extends DBObj {
  protected static $__table="inv_payments";
  public static $mod="invoicing";
  public static $sub="payments";
  
  public static $elements=array(
  "inv_id"=>array("title"=>"Rechnungsnummer","mode"=>"string","dbkey"=>"inv_invoices_id","data"=>"number"),
  "invoice_id"=>array("title"=>"Rechnung","mode"=>"process"),
  
  "amount"=>array("title"=>"Betrag (CENT!)","mode"=>"string","dbkey"=>"amount","data"=>"number"),
  "amount_fmt"=>array("title"=>"Einzelpreis netto","mode"=>"process"),
  
//  "currency"=>array("title"=>"Währung","mode"=>"select","data"=>array("","Euro"),"dbkey"=>"currency"),
  "type"=>array("title"=>"Zahlungstyp","mode"=>"select","data"=>array("Bar","Kredit/EC-Karte"),"dbkey"=>"type"),
  
  "ts"=>array("title"=>"Datum/Zeit","mode"=>"string","dbkey"=>"ts"),
  "ts_fmt"=>array("title"=>"Datum/Zeit formatiert","mode"=>"process"),
  );
  
  public static $link_elements=array(
  );
  public static $list_elements=array(
    "invoice_id",
    "ts_fmt",
    "amount_fmt",
    "type",
  );
  public static $detail_elements=array(
    "invoice_id",
    "ts_fmt",
    "amount_fmt",
    "type",
  );
  public static $edit_elements=array(
    "inv_id","ts","amount","type"
    
  );
  public static $links=array(
  );
  public function processProperty($key) {
    $ret=NULL;
    switch($key) {
      case "invoice_id":
        if($this->inv_invoices_id==0) {
          $ret="(unbekannt)";
          break;
        } else {
          $obj=Inv_Invoice::getById($this->inv_invoices_id);
          $ret=$obj->toString();
        }
      break;
      case "amount_fmt": //formatted amount
        $ret=sprintf("%.2f",($this->amount)/100);
      break;
      case "ts_fmt":
        $ret=date("d.m.Y H:i:s",$this->ts);
      break;
      
    }
    return $ret;
  }
}

plugins_register_backend_handler($plugin,"payments","list",array("Inv_Payment","listView"));
plugins_register_backend_handler($plugin,"payments","edit",array("Inv_Payment","editView"));
plugins_register_backend_handler($plugin,"payments","view",array("Inv_Payment","detailView"));
plugins_register_backend_handler($plugin,"payments","submit",array("Inv_Payment","processSubmit"));
