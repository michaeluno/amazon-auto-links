<?php 
/**
	Admin Page Framework v3.9.0b07 by Michael Uno 
	Generated by PHP Class Files Script Generator <https://github.com/michaeluno/PHP-Class-Files-Script-Generator>
	<http://en.michaeluno.jp/amazon-auto-links>
	Copyright (c) 2013-2021, Michael Uno; Licensed under MIT <http://opensource.org/licenses/MIT> */
class AmazonAutoLinks_AdminPageFramework_Utility_Interpreter extends AmazonAutoLinks_AdminPageFramework_Utility_HTMLAttribute {
    static public function getReadableListOfArray(array $aArray) {
        $_aOutput = array();
        foreach ($aArray as $_sKey => $_vValue) {
            $_aOutput[] = self::getReadableArrayContents($_sKey, $_vValue, 32) . PHP_EOL;
        }
        return implode(PHP_EOL, $_aOutput);
    }
    static public function getReadableArrayContents($sKey, $vValue, $sLabelCharLengths = 16, $iOffset = 0) {
        $_aOutput = array();
        $_aOutput[] = ($iOffset ? str_pad(' ', $iOffset) : '') . ($sKey ? '[' . $sKey . ']' : '');
        if (!in_array(gettype($vValue), array('array', 'object'))) {
            $_aOutput[] = $vValue;
            return implode(PHP_EOL, $_aOutput);
        }
        foreach ($vValue as $_sTitle => $_asDescription) {
            if (!in_array(gettype($_asDescription), array('array', 'object'))) {
                $_aOutput[] = str_pad(' ', $iOffset) . $_sTitle . str_pad(':', $sLabelCharLengths - self::getStringLength($_sTitle)) . $_asDescription;
                continue;
            }
            $_aOutput[] = str_pad(' ', $iOffset) . $_sTitle . ": {" . self::getReadableArrayContents('', $_asDescription, 16, $iOffset + 4) . PHP_EOL . str_pad(' ', $iOffset) . "}";
        }
        return implode(PHP_EOL, $_aOutput);
    }
    static public function getReadableListOfArrayAsHTML(array $aArray) {
        $_aOutput = array();
        foreach ($aArray as $_sKey => $_vValue) {
            $_aOutput[] = "<ul class='array-contents'>" . self::getReadableArrayContentsHTML($_sKey, $_vValue) . "</ul>" . PHP_EOL;
        }
        return implode(PHP_EOL, $_aOutput);
    }
    static public function getReadableArrayContentsHTML($sKey, $vValue) {
        $_aOutput = array();
        $_aOutput[] = $sKey ? "<h3 class='array-key'>" . $sKey . "</h3>" : "";
        if (!in_array(gettype($vValue), array('array', 'object'), true)) {
            $_aOutput[] = "<div class='array-value'>" . html_entity_decode(nl2br($vValue), ENT_QUOTES) . "</div>";
            return "<li>" . implode(PHP_EOL, $_aOutput) . "</li>";
        }
        foreach ($vValue as $_sKey => $_vValue) {
            $_aOutput[] = "<ul class='array-contents'>" . self::getReadableArrayContentsHTML($_sKey, $_vValue) . "</ul>";
        }
        return implode(PHP_EOL, $_aOutput);
    }
    static public function getTableOfArray(array $aArray, array $aAllAttributes = array(), array $aHeader = array(), array $aFooter = array()) {
        $_aAllAttributes = $aAllAttributes + array('table' => array(), 'tbody' => array(), 'td' => array(array(), array(),), 'tr' => array(), 't' => array(), 'ul' => array(), 'li' => array(),);
        return "<table " . self::getAttributes(self::getElementAsArray($_aAllAttributes, 'table')) . ">" . self::___getTableHeader($aHeader, $_aAllAttributes) . "<tbody " . self::getAttributes(self::getElementAsArray($_aAllAttributes, 'tbody')) . ">" . self::___getTableRows($aArray, $_aAllAttributes) . "</tbody>" . self::___getTableFooter($aFooter, $_aAllAttributes) . "</table>";
    }
    static private function ___getTableHeader(array $aHeader, array $aAllAttributes) {
        if (empty($aHeader)) {
            return '';
        }
        $_aTRAttr = self::getElementAsArray($aAllAttributes, 'tr');
        $_aTHAttr = self::getElementAsArray($aAllAttributes, 'th');
        $_aTHAttr1 = self::getElementAsArray($aAllAttributes, array('th', 0)) + $_aTHAttr;
        $_aTHAttr2 = self::getElementAsArray($aAllAttributes, array('th', 1)) + $_aTHAttr;
        $_sOutput = '';
        foreach ($aHeader as $_sKey => $_sValue) {
            $_sOutput = "<tr " . self::getAttributes($_aTRAttr) . ">" . "<th " . self::getAttributes($_aTHAttr1) . ">" . $_sKey . "</th>" . "<th " . self::getAttributes($_aTHAttr2) . ">" . $_sValue . "</th>" . "</tr>";
        }
        return "<thead>" . $_sOutput . "</thead>";
    }
    static private function ___getTableFooter(array $aFooter, array $aAllAttributes) {
        if (empty($aFooter)) {
            return '';
        }
        $_aTRAttr = self::getElementAsArray($aAllAttributes, 'tr');
        $_aTDAttr = self::getElementAsArray($aAllAttributes, 'td');
        $_aTDAttr1 = self::getElementAsArray($aAllAttributes, array('td', 0)) + $_aTDAttr;
        $_aTDAttr2 = self::getElementAsArray($aAllAttributes, array('td', 1)) + $_aTDAttr;
        $_sOutput = '';
        foreach ($aFooter as $_sKey => $_sValue) {
            $_sOutput = "<tr " . self::getAttributes($_aTRAttr) . ">" . "<td " . self::getAttributes($_aTDAttr1) . ">" . $_sKey . "</td>" . "<td " . self::getAttributes($_aTDAttr2) . ">" . $_sValue . "</td>" . "</tr>";
        }
        return "<tfoot>" . $_sOutput . "</tfoot>";
    }
    static private function ___getTableRows(array $aItem, array $aAllAttributes) {
        $_aTRAttr = self::getElementAsArray($aAllAttributes, 'tr');
        $_aTDAttr = self::getElementAsArray($aAllAttributes, 'td');
        $_aTDAttr = array_filter($_aTDAttr, 'is_scalar');
        if (empty($aItem)) {
            $_aTDAttr = array('colspan' => 2) + $_aTDAttr;
            return "<tr " . self::getAttributes($_aTRAttr) . ">" . "<td " . self::getAttributes($_aTDAttr) . ">" . __('No data found.', 'amazon-auto-links') . "</td>" . "</tr>";
        }
        $_aTDAttrFirst = self::getElementAsArray($aAllAttributes, array('td', 0)) + $_aTDAttr;
        $_aTDAttrFirst['class'] = self::___addClass('column-key', self::getElement($_aTDAttrFirst, array('class'), ''));
        $_sOutput = '';
        foreach ($aItem as $_sColumnName => $_asValue) {
            $_sOutput.= "<tr " . self::getAttributes($_aTRAttr) . ">";
            $_sOutput.= "<td " . self::getAttributes($_aTDAttrFirst) . ">" . "<p>{$_sColumnName}</p>" . "</td>";
            $_sOutput.= self::___getColumnValue($_asValue, $aAllAttributes);
            $_sOutput.= "</tr>";
        }
        return $_sOutput;
    }
    static private function ___addClass($sClassToAdd, $sClasses) {
        $_aClasses = explode(' ', $sClasses);
        $_aClasses[] = $sClassToAdd;
        return implode(' ', array_unique($_aClasses));
    }
    static private function ___getColumnValue($mValue, array $aAllAttributes) {
        $_aTDAttr = self::getElementAsArray($aAllAttributes, 'td');
        $_aTDAttr = array_filter($_aTDAttr, 'is_scalar');
        $_aTDAttrSecond = self::getElementAsArray($aAllAttributes, array('td', 1)) + $_aTDAttr;
        $_aTDAttrSecond['class'] = self::___addClass('column-value', self::getElement($_aTDAttrSecond, array('class'), ''));
        if (is_null($mValue)) {
            $mValue = '(null)';
        }
        if (is_scalar($mValue)) {
            return "<td " . self::getAttributes($_aTDAttrSecond) . ">" . "<p>{$mValue}</p>" . "</td>";
        }
        if (is_array($mValue)) {
            return self::isAssociativeArray($mValue) || self::isMultiDimensional($mValue) ? "<td " . self::getAttributes($_aTDAttrSecond) . ">" . self::getTableOfArray($mValue, $aAllAttributes) . "</td>" : "<td " . self::getAttributes($_aTDAttrSecond) . ">" . self::___getList($mValue, $aAllAttributes) . "</td>";
        }
        return "<td " . self::getAttributes($_aTDAttrSecond) . ">" . '(' . gettype($mValue) . ')' . (is_object($mValue) ? get_class($mValue) : '') . "</td>";
    }
    static private function ___getList(array $aArray, $aAllAttributes) {
        $_aULAttr = self::getElementAsArray($aAllAttributes, 'ul');
        $_aLIAttr = self::getElementAsArray($aAllAttributes, 'li');
        $_aULAttr['class'] = self::___addClass('numeric', self::getElement($_aULAttr, array('class'), ''));
        if (empty($aArray)) {
            return '';
        }
        $_sList = "<ul " . self::getAttributes($_aULAttr) . ">";
        foreach ($aArray as $_sValue) {
            $_sList.= "<li " . self::getAttributes($_aLIAttr) . ">" . $_sValue . "</li>";
        }
        $_sList.= "</ul>";
        return $_sList;
    }
    }
    