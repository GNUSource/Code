<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2015/1/22
 * Time: 12:55
 */
//require_once app_path() . "/lib/PHPExcel/PHPExcel.php";
//require_once app_path() . "/lib/PHPExcel/PHPExcel/IOFactory.php";
//require_once app_path() . "/lib/PHPExcel/PHPExcel/Writer/Excel5.php";
require_once "./../lib/PHPExcel/PHPExcel.php";
require_once "./../lib/PHPExcel/PHPExcel/IOFactory.php";
require_once "./../lib/PHPExcel/PHPExcel/Writer/Excel5.php";

class PHPExcelOperate
{
    private $phpExcel;
    private $exportType;

    public function __construct($exportType = "excel")
    {
        $this->phpExcel = new \PHPExcel();
        $this->exportType = $exportType;
        $this->setActiveSheetIndex();
    }

    public function __get($name)
    {
        if ($this->isPropertyExists($name)) {
            return $this->$name;
        }
    }

    public function __set($name, $value)
    {
        if ($this->isPropertyExists($name)) {
            $this->$name = $value;
        }
    }

    public function __call($name, $arguments)
    {
        // TODO: Implement __call() method.
    }

    /**
     * 判断属性在该类中是否存在
     *
     * @param string $propertyName 属性名称
     * @return bool true|false  返回的结果
     */
    private function isPropertyExists($propertyName)
    {
        $properties = get_class_vars(get_class($this));
        if (!array_key_exists($propertyName, $properties)) {
            return false;
        }
        return true;
    }

    /**
     * 设置文件详细信息
     */
    public function setFileProperties()
    {
        $this->phpExcel->getProperties()
            ->setCreator("Auto")
            ->setLastModifiedBy("Auto")
            ->setTitle("Microsoft Office Excel Document")
            ->setSubject("Microsoft Office Excel Document")
            ->setDescription("Microsoft Office Excel Document")
            ->setKeywords("Microsoft Office Excel Document")
            ->setCategory("Microsoft Office Excel Document");
    }

    /**
     * 设置活动的excel_sheet
     *
     * @param int $index
     * @throws PHPExcel_Exception
     */
    public function setActiveSheetIndex($index = 0)
    {
        $this->phpExcel->setActiveSheetIndex($index);
    }

    /**
     * 取得活动的excel_sheet
     *
     * @return PHPExcel_Worksheet
     */
    protected function getActiveSheet()
    {
        return $this->phpExcel->getActiveSheet();
    }

    /**
     * 为指定单元格指定内容
     *
     * @param string $pCoordinate 单元格
     * @param string $pValue 内容
     */
    public function setCellValue($pCoordinate, $pValue)
    {
        $this->getActiveSheet()->setCellValue($pCoordinate, $pValue);
    }

    /**
     * 设置单元格样式
     *
     * @param string $pCoordinate 单元格
     * @param array $style 样式数组
     */
    public function setCellStyle($pCoordinate, $style)
    {
        $this->getActiveSheet()->getStyle($pCoordinate)->applyFromArray($style);
    }

    /**
     * 设置excel中某一列的宽度
     *
     * @param string $columnName 列名
     * @param int $width 宽度
     */
    public function setColumnWidth($columnName, $width)
    {
        $this->getActiveSheet()->getColumnDimension($columnName)->setWidth($width);
    }

    /**
     * 设置excel某一行的高度
     *
     * @param int $row 行数
     * @param int $height 高度
     */
    public function setRowHeight($row, $height)
    {
        $this->getActiveSheet()->getRowDimension($row)->setRowHeight($height);
    }

    /**
     * 获取excel中单元格的默认样式
     *
     * @return PHPExcel_Style
     * @throws PHPExcel_Exception
     */
    public function getDefaultStyle() {
        return $this->phpExcel->getDefaultStyle();
    }

    /**
     * 输出excel
     *
     * @param $fileName excel名称
     */
    public function output($fileName)
    {
        $objWriter = new \PHPExcel_Writer_Excel5($this->phpExcel);
        $fileName = iconv("utf-8", "gb2312", $fileName . '_' . time() . '.xls');

        // 从浏览器直接输出$filename
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type: application/vnd.ms-excel;");
        header("Content-Type:application/octet-stream");
        header("Content-Type:application/download");
        header("Content-Disposition:attachment;filename=" . $fileName);
        header("Content-Transfer-Encoding:binary");
        $objWriter->save("php://output");


    }
}