<?php defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet as Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border as Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat as NumberFormat;
use PhpOffice\PhpSpreadsheet\Shared\Date as Date;
use PhpOffice\PhpSpreadsheet\Style\Alignment as Alignment;

class ExportExcel extends Public_Controller {
    /**
     * Constructor
    */
    function __construct()
    {
        parent::__construct();
    }

    public function exportExcelUsingSpreadSheet( $file_name, $arr_header, $arr_column ) {
        /* Spreadsheet Init */
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        /* Excel Header */
        for ($i=0; $i < count($arr_header); $i++) { 
            $huruf = toAlpha($i+1);

            $posisi = $huruf.'1';
            $sheet->setCellValue($posisi, $arr_header[$i]);

            $styleBold = [
                'font' => [
                    'bold' => true,
                ]
            ];
            $spreadsheet->getActiveSheet()->getStyle($posisi)->applyFromArray($styleBold);
        }

        $baris = 2;
        if ( !empty($arr_column) && count($arr_column) ) {
            for ($i=0; $i < count($arr_column); $i++) {
                for ($j=0; $j < count($arr_header); $j++) {
                    $huruf = toAlpha($j+1);

                    if ( isset($arr_column[ $i ][ $arr_header[ $j ] ]) ) {
                        $data = $arr_column[ $i ][ $arr_header[ $j ] ];

                        if ( $data['data_type'] == 'string' ) {
                            $sheet->setCellValue($huruf.$baris, strtoupper($data['value']));
                        }

                        if ( $data['data_type'] == 'nik' ) {
                            $sheet->getCell($huruf.$baris)->setValueExplicit($data['value'], DataType::TYPE_STRING);
                        }

                        if ( $data['data_type'] == 'text' ) {
                            $sheet->setCellValue($huruf.$baris, strtoupper($data['value']));
                            $spreadsheet->getActiveSheet()->getStyle($huruf.$baris)
                                        ->getNumberFormat()
                                        ->setFormatCode(NumberFormat::FORMAT_GENERAL);
                        }

                        if ( $data['data_type'] == 'date' ) {
                            if ( isset($data['data_format']) && !empty($data['data_format']) ) {
                                $dt = Date::PHPToExcel(DateTime::createFromFormat('!Y-m-d', substr($data['value'], 0, 10)));
                                $sheet->setCellValue($huruf.$baris, $dt);
                                $spreadsheet->getActiveSheet()->getStyle($huruf.$baris)
                                            ->getNumberFormat()
                                            ->setFormatCode($data['data_format']);
                            } else {
                                $dt = Date::PHPToExcel(DateTime::createFromFormat('!Y-m-d', substr($data['value'], 0, 10)));
                                $sheet->setCellValue($huruf.$baris, $dt);
                                $spreadsheet->getActiveSheet()->getStyle($huruf.$baris)
                                            ->getNumberFormat()
                                            ->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);
                            }
                        }

                        if ( $data['data_type'] == 'integer' ) {
                            $sheet->setCellValue($huruf.$baris, $data['value']);
                            $spreadsheet->getActiveSheet()->getStyle($huruf.$baris)
                                        ->getNumberFormat()
                                        ->setFormatCode(NumberFormat::FORMAT_NUMBER);
                        }

                        if ( $data['data_type'] == 'decimal2' ) {
                            $sheet->setCellValue($huruf.$baris, $data['value']);
                            $spreadsheet->getActiveSheet()->getStyle($huruf.$baris)
                                        ->getNumberFormat()
                                        ->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);
                        }

                        if ( isset($data['colspan']) ) {
                            $sheet->setCellValue($data['colspan'][0].$baris, $data['value']);
                            if ( isset($data['colspan'][1]) ) {
                                $spreadsheet->getActiveSheet()->mergeCells($data['colspan'][0].$baris.':'.$data['colspan'][1].$baris);
                            } else {
                                $spreadsheet->getActiveSheet()->mergeCells($data['colspan'][0].$baris.':'.$data['colspan'][0].$baris);
                            }

                            if ( isset($data['align']) ) {
                                $sheet->getStyle($data['colspan'][0].$baris)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                            }

                            if ( isset($data['text_style']) ) {
                                if ( $data['text_style'] == 'bold' ) {
                                    $sheet->getStyle($data['colspan'][0].$baris)->getFont()->setBold(true);
                                }
                            }
                        }

                        if ( isset($data['text_style']) ) {
                            if ( $data['text_style'] == 'bold' ) {
                                $sheet->getStyle($huruf.$baris)->getFont()->setBold(true);
                            }
                        }
                    }
                }

                $baris++;
            }
        } else {
            $range1 = 'A'.$baris;
            $range2 = toAlpha(count($arr_header)).$baris;

            $spreadsheet->getActiveSheet()->mergeCells("$range1:$range2");
            $sheet->setCellValue($range1, 'Data tidak ditemukan.');
        }

        $styleArray = [
            'borders' => [
                'bottom' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => '000000']],
                'top' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => '000000']],
                'right' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => '000000']],
                'left' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => '000000']],
            ],
        ];
        
        $spreadsheet->getActiveSheet()->getStyle('A1:'.toAlpha(count($arr_header)).$baris)->applyFromArray($styleArray, false);

        // try {
        //     $writer = new Xlsx($spreadsheet);
        //     $writer->save($file_name.'.xlsx');
        //     $content = file_get_contents($file_name.'.xlsx');
        // } catch(Exception $e) {
        //     exit($e->getMessage());
        // }
        
        // header("Content-Disposition: attachment; file_name=".$file_name.'.xlsx');
        
        // unlink($file_name.'.xlsx');
        // exit($content);

        /* Excel File Format */
        $writer = new Xlsx($spreadsheet);
        $writer->save('export_excel/'.$file_name.'.xlsx');

        // $this->load->helper('download');
        // force_download('export_excel/'.$file_name.'.xlsx', NULL);
    }
}
