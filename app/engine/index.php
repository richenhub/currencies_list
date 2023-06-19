<?php 

class Currency {
    public function getCurrency(array $needle__currency = []): array {
        $url = 'http://www.cbr.ru/scripts/XML_daily.asp';
        $xmlData = file_get_contents($url);
        $xml = simplexml_load_string($xmlData);
        $currencies = [];
        foreach ($xml->Valute as $valute) {
            $currency = [
                'name' => (string)$valute->Name,
                'code' => (string)$valute->CharCode,
                'rate' => (float)str_replace(',', '.', $valute->Value),
            ];
            $currencies[] = $currency;
        }
        if (empty($needle__currency)) return $currencies;
        $resultArray = [];
        foreach ($currencies as $currency) {
            if (in_array($currency['code'], $needle__currency)) {
                $resultArray[] = $currency;
            }
        }
        return $resultArray;
    }
}

class DB {
    public ?mysqli $db = null;
    public function __construct() {
        $this->db = new mysqli('db', 'root', '12345', 'dydesign');

        if ($this->db->connect_error) {
            die('Ошибка подключения: ' . $this->db->connect_error);
        }
        
        $sql = "CREATE TABLE `dydesign`.`currency` (`id` INT NOT NULL AUTO_INCREMENT , `name` VARCHAR(255) NOT NULL , `img_src` VARCHAR(255) NOT NULL , `rate` FLOAT NOT NULL , PRIMARY KEY (`id`)) ENGINE = InnoDB;";

        if ($this->db->query($sql) === TRUE) {
            echo 'Таблица \'currency\' успешно создана';
        }
    }

    function getAllCurrenciesFromDB(): array {
        $conn = $this->conn();
        if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);
        $selectQuery = "SELECT * FROM currency";
        $result = $conn->query($selectQuery);
        $array = [];
        while ($row = $result->fetch_assoc()) {
            $array[] = $row;
        }
        return $array;
    }

    function addOrUpdateCurrency($name, $img, $rate): void {
        $conn = $this->conn();
        if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);
        $name = $conn->real_escape_string($name);
        $imgSrc = $conn->real_escape_string($img);
        $rate = floatval($rate);
        $selectQuery = "SELECT * FROM currency WHERE name = '$name'";
        $result = $conn->query($selectQuery);
        if ($result->num_rows > 0) {
            $updateQuery = "UPDATE currency SET img_src = '$img', rate = $rate WHERE name = '$name'";
            if ($conn->query($updateQuery) === TRUE) {
                //echo "Currency updated successfully.";
            } else {
                echo "Error updating currency: " . $conn->error;
            }
        } else {
            $insertQuery = "INSERT INTO currency (name, img_src, rate) VALUES ('$name', '$img', $rate)";
            if ($conn->query($insertQuery) === TRUE) {
                //echo "Currency inserted successfully.";
            } else {
                echo "Error inserting currency: " . $conn->error;
            }
        }
    }

    public function add_currencies(): void {
        $currency = new Currency();
        $currencies = $currency->getCurrency();
        foreach ($currencies as $currency) {
           $this->addOrUpdateCurrency($currency['name'], strtoupper($currency['code']) . '.svg', $currency['rate']);
        }
    }

    public function conn(): mysqli {
        return $this->db;
    }
}