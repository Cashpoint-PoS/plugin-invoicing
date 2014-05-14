<?
//Invoices
class Inv_Invoice extends DBObj {
  protected static $__table="inv_invoices";
  public static $mod="invoicing";
  public static $sub="invoices";
  private $__positions=array(); //cache the positions between summary lookups, maybe use later in editing?
  private $__payments=array(); //as above, just for payments
  
  public static $elements=array(
  //""=>array("title"=>"","mode"=>"string","dbkey"=>""),
    "customer_id"=>array("title"=>"Kunde","mode"=>"one2many","dbkey"=>"crm_customers_id","data"=>"CRM_Customer"),
    "billaddr"=>array("title"=>"Rechnungsadresse","mode"=>"one2many","dbkey"=>"crm_addrs_bill_id","data"=>"CRM_Address"),
    "external_id"=>array("title"=>"Rechnungsnummer","mode"=>"string","dbkey"=>"external_id"),
    "payment_state"=>array("title"=>"Zahlstatus","mode"=>"select","data"=>array("Offen","Teilbezahlt","Bezahlt","Ausfall komplett","Ausfall Teil","Bezahlt mit Skonto"),"dbkey"=>"payment_state"),
    "bill_state"=>array("title"=>"Rechnungsstatus","mode"=>"select","data"=>array("Offen","Gesperrt"),"dbkey"=>"bill_state"),
    "total_limit"=>array("title"=>"Festgesetzte Gesamtsumme brutto (CENT!)","mode"=>"string","dbkey"=>"total_limit","data"=>"number"),
    "date"=>array("title"=>"Rechnungsdatum","mode"=>"string","dbkey"=>"date","data"=>"date"),
    "total_fmt"=>array("title"=>"Gesamtbetrag netto","mode"=>"process"),
    "total_vat_fmt"=>array("title"=>"Gesamtbetrag brutto","mode"=>"process"),
    "total"=>array("title"=>"Gesamtbetrag netto (CENT!)","mode"=>"process"),
    "total_vat"=>array("title"=>"Gesamtbetrag brutto (CENT!)","mode"=>"process"),
    "pos_amount"=>array("title"=>"Positionen","mode"=>"process"),
    "payments_done"=>array("title"=>"Bereits bezahlt (CENT!)","mode"=>"process"),
    "payments_due"=>array("title"=>"Noch offen (CENT!)","mode"=>"process"),
    "payments_done_fmt"=>array("title"=>"Bereits bezahlt","mode"=>"process"),
    "payments_due_fmt"=>array("title"=>"Noch offen","mode"=>"process"),
    
  );
  public static $link_elements=array(
  );
  public static $list_elements=array(
    "external_id",
    "date",
    "customer_id",
    "pos_amount",
    "payment_state",
    "bill_state",
    "total_fmt",
    "total_vat_fmt",
    "payments_done_fmt",
    "payments_due_fmt",
    "total_vat",
    "payments_due",
  );
  public static $detail_elements=array(
    "external_id",
    "date",
    "customer_id",
    "billaddr",
    "payment_state",
    "bill_state",
    "pos_amount",
    "total_fmt",
//    "total_limit_fmt",
    "total_vat_fmt",
    "payments_done_fmt",
    "payments_due_fmt",
  );
  public static $edit_elements=array(
    "external_id",
    "date",
    "customer_id",
    "billaddr",
    "payment_state",
    "bill_state",
  );
  public static $links=array(
  );
  public static $one2many=array(
    "Inv_Position"=>array("title"=>"Posten"),
  );
  public function commit() {
    if($this->date==""||$this->date=="0000-00-00")
      $this->date=date("Y-m-d");
    parent::commit();
    if($this->external_id=="")
      $this->external_id=sprintf("%s-%d",$this->date,$this->id);
    parent::commit();
    $this->__positions=array();
    $this->__payments=array();
  }
  public function processProperty($key) {
    if(sizeof($this->__positions)==0) {
      $this->__positions=Inv_Position::getByFilter("where inv_invoices_id=?",$this->id);
    }
    if(sizeof($this->__payments)==0)
      $this->__payments=Inv_Payment::getByOwner($this);
    
    $ret=NULL;
    switch($key) {
      case "total":
        $s=0;
        foreach($this->__positions as $p) {
          $s+=$p->getProperty("price_total");          
        }
        $ret=$s;
      break;
      case "total_fmt":
        $ret=sprintf("%.2f",($this->processProperty("total"))/100);
      break;
      case "total_vat":
        $s=0;
        foreach($this->__positions as $p) {
          $s+=$p->getProperty("price_total_vat");
        }
        $ret=$s;
      break;
      case "total_vat_fmt":
        $ret=sprintf("%.2f",($this->processProperty("total_vat"))/100);
      break;
      case "payments_done":
        $s=0;
        foreach($this->__payments as $p)
          $s+=$p->getProperty("amount");
        $ret=$s;
      break;
      case "payments_due":
        $ret=$this->processProperty("total_vat")-$this->processProperty("payments_done");
      break;
      case "payments_done_fmt":
        $ret=sprintf("%.2f",($this->processProperty("payments_done"))/100);
      break;
      case "payments_due_fmt":
        $ret=sprintf("%.2f",($this->processProperty("payments_due"))/100);
      break;
      case "pos_amount":
        $ret=sizeof($this->__positions);
      break;
    }
    return $ret;
  }
  
  public function toString() {
    return $this->external_id;
  }
}

plugins_register_backend_handler($plugin,"invoices","list",array("Inv_Invoice","listView"));
plugins_register_backend_handler($plugin,"invoices","edit",array("Inv_Invoice","editView"));
plugins_register_backend_handler($plugin,"invoices","view",array("Inv_Invoice","detailView"));
plugins_register_backend_handler($plugin,"invoices","submit",array("Inv_Invoice","processSubmit"));
