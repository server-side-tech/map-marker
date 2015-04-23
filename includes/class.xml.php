<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


/**
 * Description of class
 *
 * @author waelshowair
 */
class Xml {
    private $_fileName;
    private $_xmlString;

    public function __construct($fileName) {
        $this->_fileName = $fileName . '.xml';
        $this->_xmlString = "";
    }

    public function addHeader(){
        $this->_xmlString = "<?xml version='1.0' standalone='yes'?>\n";
    }

    public function openRootNode($tableName){
        $this->_xmlString .= "<$tableName>\n";
    }

    public function closeRootNode($tableName){
        $this->_xmlString .= "</$tableName>\n";
    }

    public function appendChildren($data){
        for($row=0;$row<count($data);$row++)
        {
            $this->_xmlString .= "\t".'<row number="'.$row.'">'."\n";
            for($column=0;$column<count($data[$row]);$column++){
                $this->_xmlString .= "\t\t".'<col number="'.$column.'">';
                $this->_xmlString .= $data[$row][$column];
                $this->_xmlString .= "</col>\n";
            }
            $this->_xmlString .="\t</row>\n\n";
        }
    }

    public function save(){
        $map = new SimpleXMLElement($this->_xmlString);
        $map->asXML($this->_fileName);
    }
}
