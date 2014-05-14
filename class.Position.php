<?
//Invoice positions
class Inv_Position extends DBObj {
  protected static $__table="inv_positions";
  public static $mod="invoicing";
  public static $sub="positions";
  
  public static $elements=array(
  "inv_id"=>array("title"=>"Rechnungsnummer","mode"=>"string","dbkey"=>"inv_invoices_id","data"=>"number"),
  "invoice_id"=>array("title"=>"Rechnung","mode"=>"process"),
  "order"=>array("title"=>"Reihenfolge auf Rechnung","mode"=>"string","dbkey"=>"inv_order"),
  "sku"=>array("title"=>"SKU/Art. Nr.","mode"=>"string","dbkey"=>"sku"),
  "shortdesc"=>array("title"=>"Beschreibung","mode"=>"string","dbkey"=>"shortdesc"),
  "longdesc"=>array("title"=>"Zus. Beschreibung","mode"=>"text","dbkey"=>"longdesc"),
  
  "price"=>array("title"=>"Einzelpreis netto (CENT!)","mode"=>"string","dbkey"=>"price","data"=>"number"),
  "price_fmt"=>array("title"=>"Einzelpreis netto","mode"=>"process"),
  "price_vat"=>array("title"=>"Einzelpreis brutto","mode"=>"process"),
  "price_vat_fmt"=>array("title"=>"Einzelpreis brutto (CENT!)","mode"=>"process"),
  
  "currency"=>array("title"=>"Währung","mode"=>"select","data"=>array("","Euro"),"dbkey"=>"currency"),
  "amount"=>array("title"=>"Menge","mode"=>"string","dbkey"=>"amount","data"=>"number"),
  "unit"=>array("title"=>"Einheit","mode"=>"select","data"=>array("Pauschal","Stück","Millimeter","Zentimeter","Meter","Gramm","Kilogramm","Tonne","Milliliter","Kubikzentimeter","Liter","Kubikmeter","Quadratzentimeter","Quadratmeter","Minute","Stunde","Sekunde","Arbeitseinheit"),"dbkey"=>"unit"),
  
  "vat_percentage"=>array("title"=>"USt-Satz","mode"=>"string","dbkey"=>"vat_percentage","data"=>"number"),
  
  "price_total"=>array("title"=>"Gesamtpreis (CENT!)","mode"=>"process"),
  "price_total_fmt"=>array("title"=>"Gesamtpreis netto","mode"=>"process"),
  "price_total_vat"=>array("title"=>"Gesamtpreis brutto (CENT!)","mode"=>"process"),
  "price_total_vat_fmt"=>array("title"=>"Gesamtpreis brutto","mode"=>"process"),
  
  "vat"=>array("title"=>"Steueranteil einzeln (CENT!)","mode"=>"process"),
  "total_vat"=>array("title"=>"Steueranteil gesamt (CENT!)","mode"=>"process"),
  "vat_fmt"=>array("title"=>"Steueranteil einzeln","mode"=>"process"),
  "total_vat_fmt"=>array("title"=>"Steueranteil gesamt","mode"=>"process"),
  
  "ts"=>array("title"=>"Datum/Zeit","mode"=>"string","dbkey"=>"ts"),
  "ts_fmt"=>array("title"=>"Datum/Zeit formatiert","mode"=>"process"),
  );
  
  public static $link_elements=array(
  );
  public static $list_elements=array(
    "invoice_id",
    "ts_fmt",
    "sku",
    "shortdesc",
    "price_fmt",
    
    "currency",
    "amount",
    "unit",
    "vat_percentage",
    "price_total_fmt",
    "price_total_vat_fmt"
  );
  public static $detail_elements=array(
    "invoice_id",
    "ts_fmt",
    "order",
    "sku",
    "shortdesc",
    "longdesc",
    "currency",
    "amount",
    "unit",
    
    "price",
    "price_fmt",
    "price_vat",
    "price_vat_fmt",
    
    "price_total",
    "price_total_fmt",
    "price_total_vat",
    "price_total_vat_fmt",
    
    "vat_percentage",
    "vat",
    "total_vat",
    "vat_fmt",
    "total_vat_fmt"
  );
  public static $edit_elements=array(
    "inv_id","order","sku","shortdesc","longdesc","price","currency","amount","unit","vat_percentage","ts"
    
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
      //price: raw
      case "price_fmt": //formatted price
        $ret=sprintf("%.2f",($this->price)/100);
      break;
      case "price_total": //total raw price without VAT
        $ret=$this->price*$this->amount;
      break;
      case "price_total_fmt": //total formatted price without VAT
        $ret=sprintf("%.2f",($this->price*$this->amount)/100);
      break;
      case "price_total_vat": //total raw price with VAT
        $ret=round(($this->price)*(1+($this->vat_percentage/100)))*$this->amount;
      break;
      case "price_total_vat_fmt": //total formatted price with VAT
        $ret=sprintf("%.2f",round(($this->price)*(1+($this->vat_percentage/100)))*$this->amount/100);
      break;
      case "price_vat": //raw price with VAT
        $ret=round(($this->price)*(1+($this->vat_percentage/100)));
      break;
      case "price_vat_fmt": //formatted price with VAT
        $ret=sprintf("%.2f",(($this->price)*(1+($this->vat_percentage/100)))/100);
      break;
      case "vat":
        $ret=round((($this->price)*(0+($this->vat_percentage/100))));
      break;
      case "total_vat":
        $ret=round((($this->price)*(0+($this->vat_percentage/100))))*$this->amount;
      break;
      case "vat_fmt":
        $ret=sprintf("%.2f",round((($this->price)*(0+($this->vat_percentage/100))))/100);
      break;
      case "total_vat_fmt":
        $ret=sprintf("%.2f",round((($this->price)*(0+($this->vat_percentage/100))))*$this->amount/100);
      break;
      case "ts_fmt":
        $ret=date("d.m.Y H:i:s",$this->ts);
      break;
      
    }
    return $ret;
  }
  
  public function toString() {
    return $this->shortdesc;
  }
}

plugins_register_backend_handler($plugin,"positions","list",array("Inv_Position","listView"));
plugins_register_backend_handler($plugin,"positions","edit",array("Inv_Position","editView"));
plugins_register_backend_handler($plugin,"positions","view",array("Inv_Position","detailView"));
plugins_register_backend_handler($plugin,"positions","submit",array("Inv_Position","processSubmit"));
