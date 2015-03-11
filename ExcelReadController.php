<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2015/3/5
 * Time: 10:53
 */
require_once '../lib/PHPExcel/PHPExcel.php';

class ExcelReadController
{
    private $filePath = '../excelFile/new_phone.xlsx';
    private $dsn = 'mysql:host=127.0.0.1;dbname=noodles';
    private $username = 'noodles';
    private $password = 'Noodles123456789';
    private $table = 'hkf_send_message_test';
    private $dbh;   //  数据库连接信息


    public function __construct()
    {
        //	实例化连接,并设置捕获错误机制
        $dbh = new PDO($this->dsn, $this->username, $this->password);
        //	设置字符集
        $dbh->query("set names 'utf8'");
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $this->dbh = $dbh;
    }

    /**
     * 读取excel
     */
    public function readExcel()
    {
        $table = $this->table;

        //  load excel
        $PHPExcel = $this->loadExcel();
        if ($PHPExcel === false) {
            return 'load excel failed.';
        }

        //  取得最大的列号、行号
        $currentSheet = $PHPExcel->getActiveSheet(0);
        $allColumn = $currentSheet->getHighestColumn();
        $allRow = $currentSheet->getHighestRow();

        //  从第二行开始输出，因为excel表中第一行为列名
        for ($currentRow = 2; $currentRow <= $allRow; $currentRow++) {
            /**从第A列开始输出*/
            $data = [];
            for ($currentColumn = 'A'; $currentColumn <= $allColumn; $currentColumn++) {
                $val = $currentSheet->getCellByColumnAndRow(ord($currentColumn) - 65, $currentRow)->getValue();
                array_push($data, $val);
            }

            try {
                $dbh = $this->dbh;

                //  先查询带插入的数据是否存在,若不存在则执行插入操作,否则则提示已存在
                $sql = "select count(*) as number from $table where name=:name and phone=:phone";
                $stmt = $dbh->prepare($sql);
                //	绑定参数
                $stmt->bindParam(':name', $data[0], PDO::PARAM_STR);
                $stmt->bindValue(':phone', $data[3], PDO::PARAM_STR);
                $stmt->execute();
                if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    if (((int)$row['number']) <= 0) {
                        $sql = "insert into $table (name, gender, localtion, phone) values
                                (:name, :gender, :localtion, :phone)";
                        $stmt = $dbh->prepare($sql);

                        //	绑定参数
                        $stmt->bindParam(':name', $data[0]);
                        $stmt->bindParam(':gender', $data[1]);
                        $stmt->bindParam(':localtion', $data[2]);
                        $stmt->bindValue(':phone', $data[3]);

                        //	执行sql语句
                        $result = $stmt->execute();

                        //	查看操作结果
                        if ($result !== false) {
                            echo 'success.' . "\t";
                        } else {
                            echo 'failed.' . "\t";
                        }
                    } else {
                        echo 'data is exists.' . "\t";
                    }
                }
            } catch (PDOException $e) {
                echo 'ERROR：' . $e->getMessage();
            }

            echo "<br/>";
        }
    }

    /**
     * update excel
     */
    public function updateExcel()
    {
        $outputFileName = 'newPhone.xlsx';
        $table = $this->table;
        //  获取excel中当前活动的工作薄
        $PHPExcel = $this->loadExcel();
        if ($PHPExcel === false) {
            return 'load excel failed.';
        }

        /**取得一共有多少行*/
        $currentSheet = $PHPExcel->getActiveSheet(0);
        $allRow = $currentSheet->getHighestRow();

        //  取得数据库中短信的发送时间
        $sendTimes = [];
        try {
            $dbh = $this->dbh;

            //  查询短信发送时间
            $sql = "select `message_send_time` as send_time from $table";
            $stmt = $dbh->prepare($sql);
            $stmt->execute();
            $sendTimes = $stmt->fetchAll();
        } catch (PDOException $e) {
            echo 'ERROR：' . $e->getMessage();
        }

        for ($currentRow = 2; $currentRow <= $allRow; $currentRow++) {
            $currentColumn = 'G';
            $currentSheet->setCellValueByColumnAndRow(ord($currentColumn) - 65, $currentRow, $sendTimes[$currentRow-2]['send_time']);
        }

        //实例化Excel写入类
        $PHPWriter = new PHPExcel_Writer_Excel2007($PHPExcel);
        ob_start();
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");
        header('Content-Disposition:attachment;filename="' .$outputFileName. '"');//输出模板名称
        header("Content-Transfer-Encoding: binary");
        header("Last-Modified:".gmdate("D, d M Y H:i:s")." GMT");
        header('Pragma: public');
        header('Expires: 30');
        header('Cache-Control: public');
        $PHPWriter->save('php://output');
    }

    private function loadExcel()
    {
        $filePath = $this->filePath;

        /**默认用excel2007读取excel，若格式不对，则用之前的版本进行读取*/
        $PHPReader = new PHPExcel_Reader_Excel2007();
        if (!$PHPReader->canRead($filePath)) {
            $PHPReader = new PHPExcel_Reader_Excel5();
            if (!$PHPReader->canRead($filePath)) {
                return false;
            }
        }
        $PHPExcel = $PHPReader->load($filePath);

        return $PHPExcel;
    }
}

$excelRead = new ExcelReadController();
//$excelRead->readExcel();
$excelRead->updateExcel();