<?php defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Reporting_model extends CI_Model
{
    public function __construct(){
        if(!$this->ion_auth->logged_in()){
            exit('Access forbidden.');
        }

        $this->documentStyle = [
            'font'  => [
                'size'  => 14,
                'name'  => 'Arial'
            ]
        ];

        $this->smallHeaderCell = [
            'font' => [
                'bold' => true,
                'size' => 14,
                'color' => ['rgb' => '000000']
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                'indent' => 5,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => 'FFFFFF',
                ],
                'endColor' => [
                    'rgb' => 'FFFFFF',
                ],
            ],
            'borders' => [
                'bottom' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => array('rgb' => '000000'),
                ],
                'top' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => array('rgb' => '000000'),
                ]
            ],
            'alignment' => [
                'wrapText' => true
            ]
        ];

        $this->headerCell = [
            'font' => [
                'bold' => true,
                'size' => 15,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                'indent' => 5,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ],
            'border' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                'color' => [
                    'rgb' => '339966',
                ]
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => '339966',
                ],
                'endColor' => [
                    'rgb' => '339966',
                ],
            ],
            'alignment' => [
                'wrapText' => true
            ]
        ];

        $this->headerCellSecondary = [
            'font' => [
                'bold' => true,
                'size' => 15,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                'indent' => 5,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ],
            'border' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                'color' => [
                    'rgb' => 'FF6600',
                ]
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => 'FF6600',
                ],
                'endColor' => [
                    'rgb' => 'FF6600',
                ],
            ],
            'alignment' => [
                'wrapText' => true
            ]
        ];

        $this->headerCellThird = [
            'font' => [
                'bold' => true,
                'size' => 15,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                'indent' => 5,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ],
            'border' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                'color' => [
                    'rgb' => '00CCFF',
                ]
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => '00CCFF',
                ],
                'endColor' => [
                    'rgb' => '00CCFF',
                ],
            ],
            'alignment' => [
                'wrapText' => true
            ]
        ];

        $this->headerCellFourth = [
            'font' => [
                'bold' => true,
                'size' => 15,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                'indent' => 5,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ],
            'border' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
                'color' => [
                    'rgb' => '1F497D',
                ]
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => '1F497D',
                ],
                'endColor' => [
                    'rgb' => '1F497D',
                ],
            ],
            'alignment' => [
                'wrapText' => true
            ]
        ];

        $this->totalCell = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => 'c9c9c9',
                ],
                'endColor' => [
                    'rgb' => 'c9c9c9',
                ],
            ]
        ];

        $this->basicCellOdd = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => '000000']
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => 'f7f5e9',
                ],
                'endColor' => [
                    'rgb' => 'f7f5e9',
                ],
            ],
            'borders' => [
                'outline' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => array('argb' => 'fffefa')
                ]
            ]
        ];

        $this->basicCellEven = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => '000000']
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => 'edece6',
                ],
                'endColor' => [
                    'rgb' => 'edece6',
                ],
            ],
            'borders' => [
                'outline' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => array('argb' => 'fffefa')
                ]
            ]
        ];

    }

    /**
     * Get membership statistics from -> to period
     */
    private function get_membership_stats($from, $to){
        $data = $this->API->subscriptions->get_subscriptions(['from' => $from, 'to' => $to]);
    
        $return = [];
        if($data->success){
            foreach($data->data as $sub){
                if(!isset($return[date('Y-m-d', strtotime($sub->createdOn))])) $return[date('Y-m-d', strtotime($sub->createdOn))] = [];
                if(!isset($return[date('Y-m-d', strtotime($sub->createdOn))][$sub->subType])) $return[date('Y-m-d', strtotime($sub->createdOn))][$sub->subType] = 0;
                $return[date('Y-m-d', strtotime($sub->createdOn))][$sub->subType]++;
            }
        }

        return $return;
    }

    /**
     * Get money statistics from -> to period
     */
    private function get_money_stats($from, $to){
        $data = $this->API->transactions->get_transactions(['from' => $from, 'to' => $to]);

        $return = [];
        if($data->success && !empty($data->data)){
            foreach($data->data as $t){
                if(!isset($return[date('Y-m-d', strtotime($t->paidOn))])) $return[date('Y-m-d', strtotime($t->paidOn))] = [];
                if(!isset($return[date('Y-m-d', strtotime($t->paidOn))][$t->transType])) $return[date('Y-m-d', strtotime($t->paidOn))][$t->transType] = 0;
                $return[date('Y-m-d', strtotime($t->paidOn))][$t->transType] += $t->value;
            }
        }

        return $return;
    }

    /**
     * Get sale statistics from -> to period
     */
    private function get_sale_stats($from, $to, $forGyms = FALSE){
        $this->load->model('pricelist_model', 'CMS'); // CMS = pricelist model
        $this->load->model('depot_model', 'depot');

        $data = $this->API->transactions->get_transactions(['from' => $from, 'to' => $to]);

        $gyms = NULL;
        if($forGyms) $gyms = $this->gyms->getAllGyms();

        $return = [];
        // TODO: Account for refunds
        if($data->success && !empty($data->data)){
            foreach($data->data as $t){

                if(!is_null($gyms)){
                    foreach($gyms as $gym){
                        $gym_id = $gym['_id']->{'$id'};

                        if(!isset($return[$gym_id])) $return[$gym_id] = array();

                        if($t->refund !== 'true' && $t->gymId == $gym_id){
                            if(!empty($t->items)){
                                // depot items
                                foreach($t->items as $item){

                                    $item_db = NULL;
                                    if(isset($item->depotId)){
                                        // depot
                                        $item_db = $this->depot->getDepotItem($item->itemId);
                                        $item_type = 'depot_' . $item_db['category'];
                                    }else{
                                        // pricelist
                                        $item_db = $this->pricelist->getPrice($item->itemId);
                                        $item_type = 'service_' . $item_db['service_type'];
                                    }
        
                                    if(!isset($return[$gym_id][$item_type])) $return[$gym_id][$item_type] = 0;
                                    $return[$gym_id][$item_type] += ($t->value - $t->vat_value);
                                }
                            }
            
                            if($t->creditTopUp){
                                // credit
                                if(!isset($return[$gym_id]['credit'])) $return[$gym_id]['credit'] = 0;
                                $return[$gym_id]['credit'] += ($t->value - $t->vat_value);
                            }
            
                            if($t->subscriptionPayment){
                                // subs
                                if(!isset($return[$gym_id]['subscriptions'])) $return[$gym_id]['subscriptions'] = 0;
                                $return[$gym_id]['subscriptions'] += ($t->value - $t->vat_value);
                            }

                        }

                    }
                }else{
                    if(!isset($return[date('Y-m-d', strtotime($t->paidOn))])) $return[date('Y-m-d', strtotime($t->paidOn))] = array();

                    if($t->refund !== 'true'){
    
                        if(!empty($t->items)){
                            // depot items
                            foreach($t->items as $item){

                                $item_db = NULL;
                                if(isset($item->depotId)){
                                    // depot
                                    $item_db = $this->depot->getDepotItem($item->itemId);
                                    $item_type = 'depot_' . $item_db['category'];
                                }else{
                                    // pricelist
                                    $item_db = $this->pricelist->getPrice($item->itemId);
                                    $item_type = 'service_' . $item_db['service_type'];
                                }
    
                                if(!isset($return[date('Y-m-d', strtotime($t->paidOn))][$item_type])) $return[date('Y-m-d', strtotime($t->paidOn))][$item_type] = 0;
                                $return[date('Y-m-d', strtotime($t->paidOn))][$item_type] += ($t->value - $t->vat_value);
                            }
                        }
        
                        if($t->creditTopUp){
                            // credit
                            if(!isset($return[date('Y-m-d', strtotime($t->paidOn))]['credit'])) $return[date('Y-m-d', strtotime($t->paidOn))]['credit'] = 0;
                            $return[date('Y-m-d', strtotime($t->paidOn))]['credit'] += ($t->value - $t->vat_value);
                        }
        
                        if($t->subscriptionPayment){
                            // subs
                            if(!isset($return[date('Y-m-d', strtotime($t->paidOn))]['subscriptions'])) $return[date('Y-m-d', strtotime($t->paidOn))]['subscriptions'] = 0;
                            $return[date('Y-m-d', strtotime($t->paidOn))]['subscriptions'] += ($t->value - $t->vat_value);
                        }
                    }
                }

            }
        }

        return $return;
    }

    /**
     * Get attendance statistics from -> to period
     */
    private function get_attendance_stats($from, $to){
        $rooms = $this->gyms->getGymRooms(current_gym_id());
        $rooms_formatted = [];

        foreach($rooms["data"] as $room){ $rooms_formatted[$room->id] = $room; }

        //$room_categories = config_item("app")["gym_rooms_categories"];
        $checkins = $this->gyms->getCheckinsForPeriod($from, $to);

        $return = [];
        if(!empty($checkins)){
            foreach($checkins as $checkin){
                $room = $rooms_formatted[$checkin->room_id];

                if(!isset($return[date('Y-m-d', strtotime($checkin->checked_in))])) $return[date('Y-m-d', strtotime($checkin->checked_in))] = [];
                if(!isset($return[date('Y-m-d', strtotime($checkin->checked_in))][$room->category])) $return[date('Y-m-d', strtotime($checkin->checked_in))][$room->category] = 0;

                $return[date('Y-m-d', strtotime($checkin->checked_in))][$room->category] += 1;
            }
        }

        return $return;
    }

    private function get_days_in_range($from, $to){
        $period = new DatePeriod(
            new DateTime($from),
            new DateInterval('P1D'),
            new DateTime($to . '+1 day')
        );
        $all_dates = [];
        foreach($period as $date){ $all_dates[] = $date->format('Y-m-d'); }

        return $all_dates;
    }

    /**
     * Generate xlsx report from array
     */
    public function generate_report_from_array($array,$filename='report',$dateCols=[]){    
        $spreadsheet = new Spreadsheet();
        
        $alphabet = range('A', 'Z');

        // set auto width for every column
        foreach($array[0] as $k=>$v){
            $spreadsheet->getActiveSheet()->getColumnDimension($alphabet[$k])->setAutoSize(true);
        }

        // style header row
        $spreadsheet->getActiveSheet()->getStyle('A1:' . $alphabet[count($array[0])] . '1' )->applyFromArray(['font' => [
            'bold' => true,
        ]]);

        // set date format 
        if(!empty($dateCols)){
            foreach ($dateCols as $col){
                $total = count($array);
                $spreadsheet->getActiveSheet()->getStyle($alphabet[$col-1]."2:".$alphabet[$col-1].$total)->getNumberFormat()->setFormatCode('DD.MM.YYYY HH:MM:SS');
            }
        }

        $spreadsheet->getActiveSheet()->fromArray($array);
        
        // redirect output to client browser
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
        header('Cache-Control: max-age=0');
        
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
    }

    /**
     * Generate a report for a manager
     */
    public function generate_manager_report($month = []){
        if(empty($month)) $month = [date('Y-m-01'), date('Y-m-t')];
        $days = (strtotime($month[1]) - strtotime($month[0])) / (60 * 60 * 24); // day count
        $all_dates = $this->get_days_in_range($month[0], $month[1]);

        // Sales
        $sales = $this->get_sale_stats($month[0], $month[1], TRUE); // True = data divided by gyms

        // XLS Setup
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $spreadsheet->getDefaultStyle()
              ->applyFromArray($this->documentStyle);

        $sheet->setCellValue('A1', 'Manažerská výsledovka za období');
        $spreadsheet->getActiveSheet()->getStyle('A1')->applyFromArray($this->headerCell);

        $sheet->setCellValue('B1', date('d.m.Y', strtotime($month[0])) . ' - ' . date('d.m.Y', strtotime($month[1])));
        $spreadsheet->getActiveSheet()->getStyle('B1')->applyFromArray($this->headerCell);
        $spreadsheet->getActiveSheet()->mergeCells('B1:D1');

        $sheet->setCellValue('A3', 'Tržby za členství (za daný měsíc)');
        $sheet->setCellValue('A4', 'Tržby za čerpání z předplacených karet');
        $sheet->setCellValue('A5', 'Tržby za Multisport');
        $sheet->setCellValue('A6', 'Tržby za ostatní partnery');
        $sheet->setCellValue('A7', 'Tržby za osobní tréninky');
        $sheet->setCellValue('A8', 'Tržby za karty');
        $sheet->setCellValue('A9', 'Tržby z prodeje zboží');
        $sheet->setCellValue('A10', 'Tržby jednorázové vstupy');
        $sheet->setCellValue('A11', 'Tržby pronájem');
        $sheet->setCellValue('A12', 'Tržby z prodeje občerstvení (nápoje + výživa)');
        $sheet->setCellValue('A13', 'Tržby za Parking');
        $sheet->setCellValue('A14', 'Tržby za ostatní služby fitcentra');
        $sheet->setCellValue('A15', 'Tržby za dětský koutek');
        $sheet->setCellValue('A16', 'Výnosy celkem'); // total

            $sheet->setCellValue('A17', 'Náklady na prodané zboží'); // small headline
            $spreadsheet->getActiveSheet()->getStyle('A17')->applyFromArray($this->smallHeaderCell);
        $sheet->setCellValue('A18', 'Nákup zboží');
        $sheet->setCellValue('A19', 'Změna stavu skladu zboží');
            $sheet->setCellValue('A20', 'Personální náklady'); // small headline
            $spreadsheet->getActiveSheet()->getStyle('A20')->applyFromArray($this->smallHeaderCell);
        $sheet->setCellValue('A21', 'Manažeři');
        $sheet->setCellValue('A22', 'Recepce');
        $sheet->setCellValue('A23', 'Instruktoři');
        $sheet->setCellValue('A24', 'Koutek');
        $sheet->setCellValue('A25', 'Lázeňský');
            $sheet->setCellValue('A26', 'Režijní náklady'); // small headline
            $spreadsheet->getActiveSheet()->getStyle('A26')->applyFromArray($this->smallHeaderCell);
        $sheet->setCellValue('A27', 'Posilovna');
        $sheet->setCellValue('A28', 'Studio');
        $sheet->setCellValue('A29', 'Welness');
        $sheet->setCellValue('A30', 'Parking');
        $sheet->setCellValue('A31', 'Úklid');
        $sheet->setCellValue('A32', 'Rekama a marketing, web');
        $sheet->setCellValue('A33', 'Telefony, internet, poštovné');
        $sheet->setCellValue('A34', 'Nájemné, energie');
        $sheet->setCellValue('A35', 'Pohonné hmoty');
        $sheet->setCellValue('A36', 'Odpisy majetku');
        $sheet->setCellValue('A37', 'Kancelářské potřeby, hygiena');
        $sheet->setCellValue('A38', 'Drobný majetek');
        $sheet->setCellValue('A39', 'Udržba software a techniky');
        $sheet->setCellValue('A40', 'Ostatní opravy a údržba');
        $sheet->setCellValue('A41', 'Účetní a organizační poradenství');
        $sheet->setCellValue('A42', 'Ostatní služby');
            $sheet->setCellValue('A43', 'Finanční náklady'); // small headline
            $spreadsheet->getActiveSheet()->getStyle('A43')->applyFromArray($this->smallHeaderCell);
        $sheet->setCellValue('A44', 'Bankovní poplatky');
        $sheet->setCellValue('A45', 'Nákladové úroky - provozní úvěr');
        $sheet->setCellValue('A46', 'Nákladové úroky - dlouhodobý úvěr');
        $sheet->setCellValue('A47', 'Výnosové úroky');

        $sheet->setCellValue('A49', 'Náklady celkem'); // total
        $sheet->setCellValue('A51', 'Účetní zisk před zdaněním'); // total

        $sheet->getColumnDimension('A')->setWidth(60);

        $totals_array = [16,48,49,51];
        $skip_cells = [48,50];
        $header_array = [17,20,26,43];

        $column = 'A';
        $gym_column = 'B';
        $offset = 1;
        $gyms = $this->gyms->getAllGyms();

        for ($gym = 1; $gym <= count($gyms)+$offset; $gym++) {

            if($gym != count($gyms) + $offset):
                $gym_name = $gyms[$gym-1]['name'];

                $sheet->setCellValue($gym_column.'2', $gym_name);
                $spreadsheet->getActiveSheet()->getStyle($gym_column.'2')->applyFromArray($this->headerCellSecondary);
                $gym_column++;
            endif;

            // Formatting
            for ($row = 3; $row <= 51; $row++){
                // Skip
                if(in_array($row, $skip_cells)) continue;

                // Even/odd colors
                if(!in_array($row, $header_array) && !in_array($row, $totals_array)){
                    if($row % 2 == 0) $sheet->getStyle($column.$row)->applyFromArray($this->basicCellEven);
                    else $sheet->getStyle($column.$row)->applyFromArray($this->basicCellOdd);
                }

                // System totals SUM
                if(in_array($row, $totals_array)) {
                    $sheet->getStyle($column.$row)->applyFromArray($this->totalCell);

                    if($column != 'A' && $row == 16):
                        $first = 3;
                        $last = 15;

                        $sheet->setCellValue($column.$row, '=SUM('.$column.$first.':'.$column.$last.')');
                    endif;
                }

                // header borders
                if(in_array($row, $header_array)) {
                    $spreadsheet->getActiveSheet()->getStyle($column.$row)->applyFromArray($this->smallHeaderCell);
                }
            }

            // DATA
            if ($column != 'A'):
                // sales
                $sale_cells_depot = [
                    1 => 12, // Výživa recepce (divide these later)
                    2 => 12, // Nápoje recepce (divide later)
                    3 => 15,
                    4 => 14,
                    5 => 14,
                    6 => 14
                ];// $config['app']['depot_item_categories'] 
                $sale_cells_services = [
                    1 => 14, // cvičební zony, todo?
                    2 => 7,
                    3 => 4,
                    4 => 14,
                    5 => 14
                ];// $config['app']['services'] 

                foreach($sales as $gym_id => $data){

                    if( $gym-1 < (count($gyms)+$offset) && $gyms[$gym-2]['_id']->{'$id'} == $gym_id ){
                        
                        foreach ( $data as $type => $total){
                            
                            if(strpos($type, 'depot_') !== false){
                                $category = isset(explode('_', $type)[1]) ? explode('_', $type)[1] : FALSE;
                                if($category) $sheet->setCellValue($column.$sale_cells_depot[$category], ( (int)$sheet->getCell($column.$sale_cells_depot[$category])->getValue() + $total));
                            }
                            else if(strpos($type, 'service_') !== false){
                                $service_type = isset(explode('_', $type)[1]) ? explode('_', $type)[1] : FALSE;
                                if($service_type) $sheet->setCellValue($column.$sale_cells_services[$service_type], ( (int)$sheet->getCell($column.$sale_cells_services[$service_type])->getValue() + $total));
                            }
                            else if(strpos($type, 'credit')  !== false){
                                $sheet->setCellValue($column.'4', ( (int)$sheet->getCell($column.'4')->getValue() + $total));
                            }
                            else if(strpos($type, 'subscriptions')  !== false){
                                $sheet->setCellValue($column.'3', ( (int)$sheet->getCell($column.'3')->getValue() + $total));
                            }
                        
                        }

                    }
                }

            endif;

            $sheet->getColumnDimension($column)->setWidth(40);
            $column++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'vysledovka_'.$month[0].'_'.$month[1].'.xlsx';

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
    }

    /**
     * Generate a daily driven report
     * Grab daily data for a set interval for generic parts of the system
     * 
     * Supply $month to get a different month than this month, [first_day, last_day]
     */
    public function generate_daily_report($month = []){
        if(empty($month)) $month = [date('Y-m-01'), date('Y-m-t')];
        $days = (strtotime($month[1]) - strtotime($month[0])) / (60 * 60 * 24); // day count
        $all_dates = $this->get_days_in_range($month[0], $month[1]);

        // Sales
        $sales = $this->get_sale_stats($month[0], $month[1]);
        // Money movement
        $money = $this->get_money_stats($month[0], $month[1]);
        // Attendance
        $attendance = $this->get_attendance_stats($month[0], $month[1]);
        // Memberships
        $memberships = $this->get_membership_stats($month[0], $month[1]);

        // XLS Setup
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $spreadsheet->getDefaultStyle()
              ->applyFromArray($this->documentStyle);

        $sheet->setCellValue('A1', 'Výnos po dnech dle skupin. Bez DPH');
        $spreadsheet->getActiveSheet()->getStyle('A1')->applyFromArray($this->headerCell);

        $sheet->setCellValue('A2', 'Sleva');
        $sheet->setCellValue('A3', 'Jednorázové vstupy');
        $sheet->setCellValue('A4', 'Předplacená karta skupinové lekce');
        $sheet->setCellValue('A5', 'Předplacená karta skupinové fitness');
        $sheet->setCellValue('A6', 'Předplacená karta skupinové welness');
        $sheet->setCellValue('A7', 'Osobní tréninky');
        $sheet->setCellValue('A8', 'Členství');
        $sheet->setCellValue('A9', 'Multisport');
        $sheet->setCellValue('A10', 'Kauce');
        $sheet->setCellValue('A11', 'Dárkový poukaz');
        $sheet->setCellValue('A12', 'Storno poplatky');
        $sheet->setCellValue('A13', 'Dětský koutek');
        $sheet->setCellValue('A14', 'Nápoje relaxace');
        $sheet->setCellValue('A15', 'Nápoje recepce');
        $sheet->setCellValue('A16', 'Nápoje automat');
        $sheet->setCellValue('A17', 'Výživa relaxace');
        $sheet->setCellValue('A18', 'Výživa recepce');
        $sheet->setCellValue('A19', 'Inbody diagnostika');
        $sheet->setCellValue('A20', 'Fitness menu');
        $sheet->setCellValue('A21', 'Půjčovna');
        $sheet->setCellValue('A22', 'Parkovné');
        $sheet->setCellValue('A23', 'Nevycvičený kredit');
        $sheet->setCellValue('A24', 'Ostatní');
        $sheet->setCellValue('A25', 'Prodej zboží');
        $sheet->setCellValue('A26', 'Poplatek za kartu');
        $sheet->setCellValue('A27', 'Solárium');
        $sheet->setCellValue('A28', 'Pronájem prostor');
        $sheet->setCellValue('A29', 'Celkem'); // total

        $sheet->setCellValue('A31', 'Inkaso peněz včetně DPH dle způsobu úhrady');
        $spreadsheet->getActiveSheet()->getStyle('A31')->applyFromArray($this->headerCellSecondary);

        $sheet->setCellValue('A32', 'Hotově');
        $sheet->setCellValue('A33', 'Kartou');
        $sheet->setCellValue('A34', 'Na účet');
        $sheet->setCellValue('A35', 'Multisport');
        $sheet->setCellValue('A36', 'Ostatní partneři');
        $sheet->setCellValue('A37', 'Celkem'); // total

        $sheet->setCellValue('A39', 'Návštěvnost na jednotlivých aktivitách');
        $spreadsheet->getActiveSheet()->getStyle('A39')->applyFromArray($this->headerCellThird);

        $sheet->setCellValue('A40', 'Fitness');
        $sheet->setCellValue('A41', 'Relaxace');
        $sheet->setCellValue('A42', 'Skupinové lekce');
        $sheet->setCellValue('A43', 'Vstup');

        $sheet->setCellValue('A45', 'Rozbor členství');
        $spreadsheet->getActiveSheet()->getStyle('A45')->applyFromArray($this->headerCellFourth);

        $sheet->setCellValue('A46', 'Členství Basic Unlimited');
        $sheet->setCellValue('A47', 'Členství Basic Student');
        $sheet->setCellValue('A48', 'Členství Off peak');
        $sheet->setCellValue('A49', 'Členství Basic Quarterly');
        $sheet->setCellValue('A50', 'Členství Platinum');
        $sheet->setCellValue('A51', 'Členství Platinum Student');
        $sheet->setCellValue('A52', 'Členství Platinum Quarterly');
        $sheet->setCellValue('A53', 'Členství Trial');
        $sheet->setCellValue('A54', 'Celkem'); // total

        $sheet->getColumnDimension('A')->setWidth(60);

        $header_array = [1,45,39,31];
        $totals_array = [54,37,29];
        $skip_cells = [44,38,30];

        $column = 'A';
        $offset = 4;

        for ($day = 2; $day <= (round($days)+$offset); $day++) {

            // Formatting
            for ($row = 1; $row <= 54; $row++){
                if(in_array($row, $skip_cells)) continue;

                if(!in_array($row, $header_array) && !in_array($row, $totals_array)){
                    if($row % 2 == 0) $sheet->getStyle($column.$row)->applyFromArray($this->basicCellEven);
                    else $sheet->getStyle($column.$row)->applyFromArray($this->basicCellOdd);
                }

                if(in_array($row, $header_array)){
                    if($row == 1) $sheet->getStyle($column.$row)->applyFromArray($this->headerCell);
                    if($row == 31) $sheet->getStyle($column.$row)->applyFromArray($this->headerCellSecondary);
                    if($row == 39) $sheet->getStyle($column.$row)->applyFromArray($this->headerCellThird);
                    if($row == 45) $sheet->getStyle($column.$row)->applyFromArray($this->headerCellFourth);

                    if($column != 'A'):
                        if($day != (round($days)+$offset)) $sheet->setCellValue($column.$row, date('d.m.', strtotime($all_dates[$day-3])));
                        else $sheet->setCellValue($column.$row, 'Celkem');
                    endif;
                }else{
                    // Total values

                    if(in_array($row, $totals_array)) {
                        // Daily sum ups
                        $sheet->getStyle($column.$row)->applyFromArray($this->totalCell);

                        if($column != 'A'):
                            $first = NULL;
                            $last = NULL;

                            if($row == 29){ $first = 1; $last = 28; }
                            if($row == 37){ $first = 32; $last = 36; }
                            if($row == 54){ $first = 46; $last = 53; }

                            $sheet->setCellValue($column.$row, '=SUM('.$column.$first.':'.$column.$last.')');
                        endif;
                    }
                    if($day == (round($days)+$offset)) {
                        // Whole range sum ups
                        if(!in_array($row, $totals_array)) $sheet->getStyle($column.$row)->applyFromArray($this->totalCell);

                        if($column != 'A' && !in_array($row, $totals_array)):
                            // previous (before last) col
                            $len = strlen($column);
                            $prev_col = ($len > 1) ? $column[0] . chr(ord($column[$len - 1]) - 1) : chr(ord($column[$len - 1]) - 1);

                            $sheet->setCellValue($column.$row, '=SUM(B'.$row.':'.$prev_col.$row.')');
                        endif;
                    }
                }
            }
 
            // DATA
            if ($column != 'A'):
                // attendance
                $attendance_cells = [1=>40, 2=>41, 3=>42, 4=>43];
                foreach($attendance as $date => $data){
                    if( $day != (round($days)+$offset) && $all_dates[$day-3] == $date ){
                        foreach($data as $category => $total){
                            if(isset($attendance_cells[$category])) $sheet->setCellValue($column.$attendance_cells[$category], $total);
                        }
                    }
                }

                // memberships
                $memberships_cells = ['basic_unlimited'=>46,'basic_student'=>47,'off_peak'=>48,'basic_quarterly'=>49,'platinum'=>50,'platinum_student'=>51,'platinum_quarterly'=>52,'trial'=>53]; // mapping []
                foreach($memberships as $date => $data){
                    if( $day != (round($days)+$offset) && $all_dates[$day-3] == $date ){
                        foreach($data as $tag => $total){
                            if(isset($memberships_cells[$tag])) $sheet->setCellValue($column.$memberships_cells[$tag], $total);
                        }
                    }
                }

                // money
                $money_cells = [1=>32,2=>33,NULL=>34,4=>35,NULL=>36]; // mapping [] // TODO obviously
                foreach($money as $date => $data){
                    if( $day != (round($days)+$offset) && $all_dates[$day-3] == $date ){
                        foreach($data as $type => $total){
                            if($type === 3) continue; // kredit?
                            if(isset($money_cells[$type])) $sheet->setCellValue($column.$money_cells[$type], $total);
                        }
                    }
                }

                // sales
                $sale_cells_depot = [
                    1 => 18, // Výživa recepce (divide these later)
                    2 => 15, // Nápoje recepce (divide later)
                    3 => 13,
                    4 => 11,
                    5 => 27,
                    6 => 24
                ];// $config['app']['depot_item_categories'] 
                $sale_cells_services = [
                    1 => 3, // cvičební zony, todo?
                    2 => 7,
                    3 => 4,
                    4 => 6,
                    5 => 24
                ];// $config['app']['services'] 

                foreach($sales as $date => $data){
                    if( $day != (round($days)+$offset) && $all_dates[$day-3] == $date ){
                        
                        foreach ( $data as $type => $total){
                            
                            if(strpos($type, 'depot_') !== false){
                                $category = isset(explode('_', $type)[1]) ? explode('_', $type)[1] : FALSE;
                                if($category) $sheet->setCellValue($column.$sale_cells_depot[$category], ( (int)$sheet->getCell($column.$sale_cells_depot[$category])->getValue() + $total));
                            }
                            else if(strpos($type, 'service_') !== false){
                                $service_type = isset(explode('_', $type)[1]) ? explode('_', $type)[1] : FALSE;
                                if($service_type) $sheet->setCellValue($column.$sale_cells_services[$service_type], ( (int)$sheet->getCell($column.$sale_cells_services[$service_type])->getValue() + $total));
                            }
                            else if(strpos($type, 'credit')  !== false){
                                $sheet->setCellValue($column.'23', ( (int)$sheet->getCell($column.'23')->getValue() + $total));
                            }
                            else if(strpos($type, 'subscriptions')  !== false){
                                $sheet->setCellValue($column.'8', ( (int)$sheet->getCell($column.'8')->getValue() + $total));
                            }
                        
                        }

                    }
                }

            endif;

            $column++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'denni_report_'.$month[0].'_'.$month[1].'.xlsx';

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
    }

}