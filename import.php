<?php
class Import
{
    private $mapowanieKolumn = [
        0 => 'numer_umowy',
        1 => 'imie_nazwisko',
        2 => 'pesel/nip',
        3 => 'telefon',
        4 => 'ulica',
        5 => 'numer_domu',
        6 => 'numer_lokalu',
        7 => 'miasto',
        8 => 'kod_pocztowy',
        9 => 'nr_rachunku',
        10 => 'kapital',
        11 => 'odsetki',
        12 => 'prowizja',
        13 => 'saldo',
        14 => 'kwota_pozyczki',
        15 => 'data_umowy',
        16 => 'wplaty'
    ];

    private $data;

    private $connection;


    public function __construct($connectionParams)
    {
        $this->db_donnect(
            $connectionParams['host'],
            $connectionParams['db_user'],
            $connectionParams['db_password'],
            $connectionParams['db_name']
        );
    }

    public function db_donnect($host, $db_user, $db_password, $db_name)
    {
        $this->connection = new mysqli($host, $db_user, $db_password, $db_name);

        if ($this->connection->connect_error) {
            echo "Failed to connect to MySQL: " . mysqli_connect_error();
        }
    }

    public function import_data()
    {
        $dane = [];
        $handle = fopen($_FILES['file']['name'], "r");

        $i = 0;
        while ($data = fgetcsv($handle, 0, ';')) {
            if ($i == 0 && count($data) != count($this->mapowanieKolumn)) {
                echo ' Error, wrong rows number';
                exit();
            }
            foreach ($data as $key => $value) {
                if ($i == 0 && $this->mapowanieKolumn[$key] != $value) {
                    echo 'Error, wrong rows name';
                    exit();
                }
                $dane[$i][$this->mapowanieKolumn[$key]] = $value;
            }
            $i++;
        }
        unset($dane[0]);
        fclose($handle);
        echo "<script>alert('Import done');</script>";
        $this->data = $dane;
    }

    public function process_data()
    {
        $iterator = 1;
        echo 'Rozpoczynam procesowanie danych <br>';
        foreach ($this->data as $row) {
            echo 'Wiersz nr ' . $iterator . '<br>';
            // echo '<pre>';
            // var_dump($row);
            // echo '</pre>';


            if (!$this->validateName($row['imie_nazwisko'])) {
                continue;
            }

            $validNipPesel = $this->checkNipPesel($row["pesel/nip"]);

            if (1 == $validNipPesel) {
                $insertDebtor = 'INSERT INTO debtor (nazwa, nip) VALUES 
                ("' . $row["imie_nazwisko"] . '", ' . $row["pesel/nip"] . ');';
            } elseif (2 == $validNipPesel) {
                $insertDebtor = 'INSERT INTO debtor (nazwa, pesel) VALUES 
                ("' . $row["imie_nazwisko"] . '", ' . $row["pesel/nip"] . ');';
            } else {
                echo 'Nie rozpoznano numeru NIP/PESEL - ' . $row["pesel/nip"];
                continue;
            }






            $adress = $this->validateAddress($row);
            $telefon = $this->processNumber($row['telefon']);

            if (!$adress) {
                echo 'Błedny adresu';
                continue;
            } elseif (!$telefon) {
                echo 'Błędny numeru telefonu';
                continue;
            } elseif (!$this->validateIfString($row['miasto'])) {
                echo 'Błędna walidacja pola miasto';
                continue;
            } elseif (!$this->validateCityCode($row['kod_pocztowy'])) {
                echo 'Błędna walidacja pola kod_pocztowy';
                continue;
            }

            $insertContact = 'INSERT INTO contact (debtor_id, telefon, adres, miasto, kod) VALUES 
                (%d, "' . $telefon . '", "' . $adress . '", "' . $row['miasto'] . '", "' . $row['kod_pocztowy'] . '" );';





            $kapital = $this->processNumber($row['kapital']);
            $odsetki = $this->processNumber($row['odsetki']);
            $prowizja = $this->processNumber($row['prowizja']);
            $saldo = $this->processNumber($row['saldo']);
            $kwotaPozyczki = $this->processNumber($row['kwota_pozyczki']);

            if (!$kapital || !$odsetki || !$prowizja || !$saldo || !$kwotaPozyczki) {
                echo 'Błędna wartość numeryczna';
                continue;
            } elseif (!$this->validateFinancialAgreement($row['numer_umowy'])) {
                echo 'Błędna walidacja pola numer_umowy';
                continue;
            } elseif (!$this->validateDate($row['data_umowy'])) {
                echo 'Błędna walidacja pola data_umowy';
                continue;
            }

            $contractDate = date('Y-m-d', strtotime($row['data_umowy']));

            $insertFinancialCondition = 'INSERT INTO financial_condition (debtor_id,numer_umowy,numer_rachunku,kapital,odsetki,prowizja,saldo,kwota_pozyczki,data_umowy ) VALUES 
            (%d, "' . $row['numer_umowy'] . '", "' . $row['nr_rachunku'] . '", "' . $kapital . '", "' . $odsetki . '", "' . $prowizja . '", "' . $saldo . '", "' . $kwotaPozyczki . '",  "' . $contractDate . '" );';



            $wplaty = $this->validatePayments($row['wplaty']);
            if (!$wplaty) {
                echo 'Błędna wartość pola wpłaty';
                continue;
            }

            $insertPayments = [];
            foreach ($wplaty as $wplata) {
                $paymentDate = date('Y-m-d', strtotime($wplata['data']));
                $paymentValue = $this->processNumber($wplata['kwota']);

                if (!$paymentValue) {
                    echo 'Błędna kwota wpłaty: ' . $paymentValue;
                    continue;
                }


                $insertPayments[] = 'INSERT INTO payment (financial_id, kwota, data_wplaty ) VALUES (%d,' . $paymentValue . ', "' . $paymentDate . '");';
            }

            $this->executeSQL($insertDebtor, $insertContact, $insertFinancialCondition, $insertPayments);
            $iterator++;
        }
    }

    private function executeSQL($insertDebtor, $insertContact, $insertFinancialCondition, $insertPayments)
    {
        $this->connection->query($insertDebtor);


        $debtorId = $this->connection->insert_id;
        var_dump($debtorId);

        if ($debtorId) {
            echo "New record created successfully" . "<br>";
        } else {
            echo "Error: " . $insertDebtor . "<br>" . $this->connection->error;
            return;
        }

        $insertPreparedContact = sprintf($insertContact, $debtorId);
        $insertPreparedFinancial = sprintf($insertFinancialCondition, $debtorId);

        $this->connection->query($insertPreparedContact);
        $this->connection->query($insertPreparedFinancial);

        $financialId = $this->connection->insert_id;


        foreach ($insertPayments as $insert) {
            $insertPreparedPayments = '';

            $insertPreparedPayments = sprintf($insert, $financialId);
            $this->connection->query($insertPreparedPayments);

            if (!$this->connection->error) {
                echo "New payment created successfully" . "<br>";
            } else {
                echo "Error:" . $this->connection->error . "</br>";
                return;
            }
        }

        echo 'Wszystkie rekordy zostały dodane poprawnie.' . "<br>" . "<br>";
    }

    private function validatePayments($wplaty)
    {
        if (empty($wplaty)) {
            echo ' Brak wpłat!';
            return [];
        }
        $wplaty_ar = explode('|', $wplaty);

        $arr_kwota = [];
        $arr_data = [];
        $arr_wplata = [];

        foreach ($wplaty_ar as $key => $value) {
            if ($key % 2 == 0) {
                $arr_kwota[] = $value;
            } else {
                $arr_data[] = $value;
            }
        }

        for ($i = 0; $i < count($arr_kwota); $i++) {
            $arr_wplata[$i]['kwota'] = $arr_kwota[$i];
            $arr_wplata[$i]['data'] = $arr_data[$i];
        }

        return $arr_wplata;
    }

    private function validateAddress($data)
    {
        if (empty($data["numer_lokalu"]) && empty($data["numer_domu"])) {
            $address =  $data["ulica"];
        } elseif (empty($data["numer_lokalu"])) {
            $address =  $data["ulica"] . ' ' .  $data["numer_domu"];
        } elseif (empty($data["numer_domu"])) {
            $address =  $data["ulica"] . ' ' . $data["numer_lokalu"];
        } else {
            $address =  $data["ulica"] . ' ' .  $data["numer_domu"] . '/' . $data["numer_lokalu"];
        }

        if (is_string($address)) {
            return trim($address);
        } else {
            echo ("error; Nieprawidłowy adres - " .  $address);
        }
    }
    private function checkNipPesel($data)
    {
        $strlth =  strlen($data);

        if ($strlth == 10) {
            return 1;
        } else if ($this->validatePesel($data)) {
            return 2;
        } else {
            echo ("error; NIP or pesel is needed");
        }
    }

    public function validatePesel($str)
    {
        if (!preg_match('/^[0-9]{11}$/', $str)) {
            return false;
        }

        $arrSteps = array(1, 3, 7, 9, 1, 3, 7, 9, 1, 3);
        $sum = 0;
        for ($i = 0; $i < 10; $i++) {
            $sum += $arrSteps[$i] * $str[$i];
        }
        $int = 10 - $sum % 10;
        $controlNr = ($int == 10) ? 0 : $int;
        if ($controlNr == $str[10]) {
            return true;
        }
        return false;
    }

    private function validateName($name)
    {
        if ($this->validateIfString($name) && $this->checkIfNotEmpty($name)) {
            return true;
        }

        return false;
    }

    private function validateIfString($data)
    {

        if (is_string($data)) {
            return true;
        }

        echo ("error; nie string");
        return false;
    }

    private function checkIfNotEmpty($data)
    {

        if (!empty($data)) {
            return true;
        }

        echo ("error; puste pole");
        return false;
    }

    private function processNumber($number)
    {
        $convertValue = str_replace(',', '.', $number);
        if ($this->validateIfNumber($convertValue)) {
            return $convertValue;
        }

        return false;
    }

    private function validateIfNumber($data)
    {

        if (is_numeric($data)) {
            return true;
        } else {
            echo ("error; nie number =" . $data);
        }
    }


    private function validateDate($date)
    {

        $regexDate = '/^(?:(?:31(\/|-|\.)(?:0?[13578]|1[02]))\1|(?:(?:29|30)(\/|-|\.)(?:0?[13-9]|1[0-2])\2))(?:(?:1[6-9]|[2-9]\d)?\d{2})$|^(?:29(\/|-|\.)0?2\3(?:(?:(?:1[6-9]|[2-9]\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00))))$|^(?:0?[1-9]|1\d|2[0-8])(\/|-|\.)(?:(?:0?[1-9])|(?:1[0-2]))\4(?:(?:1[6-9]|[2-9]\d)?\d{2})$/';

        if (preg_match($regexDate, trim($date))) {
            return true;
        } else {
            echo ("error; nieprawidłowy format danych : data_umowy powinna być w formacie: dd/mm/yyyy, dd-mm-yyyy or dd.mm.yyyy<br>");
            return false;
        }
    }
    private function validateCityCode($data)
    {

        $regexCityCode = '/^[0-9]{2}-[0-9]{3}$/';

        if (preg_match($regexCityCode, trim($data))) {
            return true;
        } else {
            echo ("City code must be in 00-000 format");
            return false;
        }
    }
    private function validateFinancialAgreement($data)
    {

        $regexFinancialAgreement = '/^UM[0-9]{9}$/';

        if (preg_match($regexFinancialAgreement, trim($data))) {
            return true;
        } else {
            echo ("Financial Agreement number must be in UM000000000 format");
            return false;
        }
    }
}
