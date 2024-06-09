<?php defined('BASEPATH') OR exit('No direct script access allowed');

use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\Printer;
class TMPrinter{
    
    protected $ci;

    public function __construct(){
        $this->ci =& get_instance();
    }

    public function connectToPrinter(){
        $connector = new FilePrintConnector("/dev/usb/lp0");
        //$connector = new FilePrintConnector('php://stdout');
        return new Printer($connector);
    }

    public function openCashdesk(){
        $printer = $this->connectToPrinter();
        $printer -> pulse();
        $printer -> close();
    }

    public function print(array $receipt, bool $cashdesk = false){
        $line = "----------------------------------------------------------------\n";
        $space = "    ";
        $date = date('j.n.Y H:m:s');
        $general_info = json_decode($this->ci->gyms->getGymSettings(['general_info'])[0]['data']);
        foreach ($this->ci->gyms->getGymSettings(['general_info','subject_info']) as $k => $v){
            ${$v['type']}=json_decode($v['data']);
        }          
        
        $logo = EscposImage::load(config_item('app')['img_folder']."logo_tm_printer.png");  
        
        $printer = $this->connectToPrinter();

        // open cashdesk
        if($cashdesk) $printer -> pulse(); 

        // set Font
        $printer -> setFont(Printer::FONT_B);

        // Top logo
        $printer -> setJustification(Printer::JUSTIFY_CENTER);
        $printer -> graphics($logo);
        $printer -> feed();
        $printer -> text("$subject_info->name\n$subject_info->street\n$subject_info->zip $subject_info->town\nIČO:$subject_info->company_id\n");  
        $printer -> feed(2);

        // User and date
        $printer -> text(new item('Tisk provedl: '.gym_users_name(), null, null, null, $date, true));

        // Items
        $printer -> setJustification(Printer::JUSTIFY_LEFT);
        $printer -> text($line);
        $printer -> setEmphasis(true); // bold font on
        $printer -> text(new item('Položka', 'Množ', 'DPH', 'Kč/jed', 'Cena Kč'));
        $printer -> setEmphasis(false); // bold font off
        $totalValue = $totalDiscount = $dph21 = $dph15 = $dph21Base = $dph15 = 0;
        foreach ($receipt['items'] as $item) {
            $printer -> text(new item($item['title'], $item['amount'], $item['dph'], $item['price'], $item['price']*$item['amount']));
            
            // Multisport item
            if($item['id'] == $receipt['multisportItem']){
                $printer -> text(new item($space."hrazeno multisport kartou", null, null, null, "-".$item['price'], true));
                $item['amount']--;
            }

            // Discount
            if($item['discount']>0){
                $discountValue = number_format($item['price']/100 * $item['discount'], 2); 
                $printer -> text(new item($space."sleva ".$item['discount'].'%', null, null, null, "-".$discountValue, true));
                $totalDiscount += $discountValue;
            }
            
            $totalValue += ($item['price'] * $item['amount']);
            // VAT
            if($item['dph']==0.21){
                $taxBase = ($item['price'] / (1+$item['dph']));
                $dph21Base += $taxBase * $item['amount'];;
                $dph21 += ( $item['price'] - $taxBase ) * $item['amount'];
            } else if ($item['dph']==0.15){
                $taxBase = ($item['price'] / (1+$item['dph']));
                $dph15Base += $taxBase * $item['amount'];
                $dph15 += ( $item['price'] - ($item['price'] / (1+$item['dph'])) ) * $item['amount'];
            }
        }

        // Total price Info
        $printer -> text($line);
        $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
        $printer -> text(new item('Celkem', null, null, null, $totalValue-$totalDiscount. " Kč", true, true));
        $printer -> selectPrintMode();
        $printer -> setFont(Printer::FONT_B);

        $printer -> feed();

        // Discount
        if($totalDiscount > 0){
            $printer -> text(new item('Sleva byla', null, null, null, $totalDiscount. " Kč", true));
        }

        if(($totalValue-$totalDiscount) > 0){ // print purchase types only if client must pay some money
            $printer -> setEmphasis(true);
            $printer -> text("Platba:\n");
            $printer -> setEmphasis(false);
            // Purchase types
            foreach($receipt['purchaseTypes'] as $pt=>$price){
                if($pt==4) continue; // multisport is hidden
                $tc = $this->ci->payments->returnTransCategories();
                $printer -> text(new item($space.$tc[$pt]['value'], null, null, null, $price. " Kč", true));
            }
        }

        $printer -> feed();

        // VAT 21 %
        if($dph21 > 0){
            $printer -> text(new item('DPH 21.00%', null, null, null, number_format($dph21, 2, '.', ''). " Kč", true));
            $printer -> text(new item('Základ DPH 21.00%', null, null, null, number_format($dph21Base, 2, '.', ''). " Kč", true));
        }
        
        // VAT 15 %
        if($dph15 > 0){
            $printer -> text(new item('DPH 15.00%', null, null, null, number_format($dph15, 2, '.', ''). " Kč", true));
            $printer -> text(new item('Základ DPH 15.00%', null, null, null, number_format($dph15Base, 2, '.', ''). " Kč", true));            
        }
        
        // Trans info
        $printer -> text($line);
        $printer -> setJustification(Printer::JUSTIFY_CENTER);
        //$printer -> text("DIČ:$subject_info->vat_id\n");
        $printer -> text("ID provozovny:".$receipt['gymCode']."\n");
        $printer -> text("ID pokladny:".$receipt['checkoutId']."\n");
        $printer -> text("ID účtenky:".$receipt['receiptId']."\n");
        //$printer -> text("$eet_id\n");
        //$printer -> text($receipt['transactionId']."\n");
        $printer -> setEmphasis(true);
        $printer -> text("Tržba v běžném režimu\n");
        $printer -> setEmphasis(false);


        // Footer
        $printer -> feed(2);
        $printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
        $printer -> text("www.gymit.cz\n");
        $printer -> feed();
        $printer -> cut();
        
        $printer -> close();
        return $printer;
    }

}

/* A wrapper to do organise item names & prices into columns */
class item {

    private $name;
    private $unitPrice;
    private $price;
    private $amount;
    private $vat;
    private $twoCols;
    private $bold;

    public function __construct($name='', $amount='', $vat='', $unitPrice='', $price='', $twoCols = false, $doubleWidth = false){
        $this->name = $name;
        $this->amount = $amount;
        $this->vat = $vat;
        $this->unitPrice = $unitPrice;
        $this->price = $price;
        $this->twoCols = $twoCols;
        $this->doubleWidth = $doubleWidth;
    }
    
    public function __toString() {
        $amountCol = 5; $vatCol = 5; $unitPriceCol = 8; $priceCol = 8;
        $rightCols = 26;
        $leftCols = 38;

        if($this->twoCols){
            $priceCol = $amountCol+$vatCol+$unitPriceCol+$priceCol;
            $leftCols = $leftCols;
        } 

        if($this->doubleWidth){
            $priceCol = $priceCol/2 - 4;
            $leftCols = $leftCols/2 - 4;
        }
        
        $left = $this->mb_str_pad($this->name, $leftCols) ;

        if(is_numeric($this->vat)) $this->vat = ($this->vat*100).'%';
        
        //$sign = ($this->crownSign ? ' Kč' : '');
        $sign = '';

        $amount = $this->mb_str_pad($this->amount . $sign, $amountCol, ' ', STR_PAD_LEFT);
        $vat = $this->mb_str_pad($this->vat . $sign, $vatCol, ' ', STR_PAD_LEFT);
        $unitPrice = $this->mb_str_pad($this->unitPrice . $sign, $unitPriceCol, ' ', STR_PAD_LEFT);
        $price = $this->mb_str_pad($this->price . $sign, $priceCol, ' ', STR_PAD_LEFT);

        if($this->twoCols) return $left . $price . "\n";
        else return $left . $amount . $vat . $unitPrice . $price . "\n";
    }

    public function mb_str_pad($input, $length, $padding = ' ', $padType = STR_PAD_RIGHT, $encoding = 'UTF-8'){
        $result = $input;
        if (($paddingRequired = $length - mb_strlen($input, $encoding)) > 0) {
            switch ($padType) {
                case STR_PAD_LEFT:
                    $result =
                        mb_substr(str_repeat($padding, $paddingRequired), 0, $paddingRequired, $encoding).
                        $input;
                    break;
                case STR_PAD_RIGHT:
                    $result =
                        $input.
                        mb_substr(str_repeat($padding, $paddingRequired), 0, $paddingRequired, $encoding);
                    break;
                case STR_PAD_BOTH:
                    $leftPaddingLength = floor($paddingRequired / 2);
                    $rightPaddingLength = $paddingRequired - $leftPaddingLength;
                    $result =
                        mb_substr(str_repeat($padding, $leftPaddingLength), 0, $leftPaddingLength, $encoding).
                        $input.
                        mb_substr(str_repeat($padding, $rightPaddingLength), 0, $rightPaddingLength, $encoding);
                    break;
                default: 
                    trigger_error('mb_str_pad: Unknown padding type ('.$padType.')', E_USER_ERROR);
                    break;
            }
        }
    
        return $result;
    }
}
