<?php


class Danutest extends MY_Controller
{
    var $API = "";

    public function __construct()
    {
        parent::__construct();
        // $this->load->library('form_validation');
        // $this->load->library('curl');
        $this->load->model('M_Global');
        $this->load->library('excel');
    }

    public function get_token()
    {
            $key = "SB-Mid-client-IOZKb8wla_E2DQEf";
            $card_num = "4811111111111114";
            $card_cvv = "123";
            $card_exp_month = "12";
            $card_exp_year = "2025";

            $curl = curl_init();

            curl_setopt_array($curl, [
              CURLOPT_URL => "https://api.sandbox.midtrans.com/v2/token?client_key=$key&card_number=$card_num&card_cvv=$card_cvv&card_exp_month=$card_exp_month&card_exp_year=$card_exp_year",
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 30,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "GET",
              CURLOPT_HTTPHEADER => [
                "accept: application/json"
              ],
            ]);

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            if ($err) {
              echo "cURL Error #:" . $err;
            } else {
              echo $response;
            }
    }

    public function test_stok()
    {

      $this->load->view('main/danu_test/test');
    }

    public function import_stok()
    {
              $data = [];
        if (isset($_FILES['excel_file'])) {
            $inputFileName = $_FILES['excel_file']['tmp_name'];
            
            // Assuming PHPExcel is correctly included and autoloaded
            $objPHPExcel = PHPExcel_IOFactory::load($inputFileName);
            $sheetData = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);

            array_shift($sheetData);
            $no = 1;

            foreach ($sheetData as $row) {
                $temp = explode("-", $row['A']);
                
                if (count($temp) < 2) {
                    continue; // Skip rows where the format is not as expected
                }

                $q = $temp[0];
                $s = $temp[1];

                $db = "SELECT SizePivot.SizePivotID, Product.ColorLabel, Product.ProductID, Product.ProductName, Size.SizeLabel, SizePivot.Stock 
                      FROM Product
                      LEFT JOIN SizePivot ON Product.ProductID = SizePivot.ProductID 
                      LEFT JOIN Size ON SizePivot.SizeID = Size.SizeID 
                      WHERE Product.CountryID = 1 AND Product.ProductCode = $q
                      AND Size.SizeLabel = $s";

                // Assuming $this->M_Global->globalquery can take parameters
                $data_db = $this->M_Global->globalquery($db)->result_array()[0];

                if (!empty($data_db)) {
                    $data_db = $data_db[0];
                } else {
                    continue; // Skip if no data is returned from the query
                }

                $data[] = [
                    "ProductCode" => $q,
                    "ProductSize" => $s,
                    "ProductName" => $row['B'],
                    "StockForstok" => $row['C'],
                    "StockAdmin" => $row['D'],
                    "DariDbSize" => $data_db['SizeLabel']
                ];

                      if ($no == 10) {
                          break; // Use break instead of die to exit the loop
                      }

                      $no++;
                  }

          echo json_encode($data);
          
          }
    }

}