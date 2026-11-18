<?php

namespace App\Services;

use ZipArchive;

/**
 * Builds the sample guest list as a real .xlsx file. The WhatsApp and Meja
 * columns are formatted as Text, so Excel keeps the leading 0 and never turns
 * a phone number into scientific notation (6,28E+11), even for rows the
 * customer types in later.
 */
class GuestTemplateXlsx
{
    private const HEADER = ['Nama', 'WhatsApp', 'Grup', 'Meja'];

    private const SAMPLES = [
        ['Bapak Joko Susilo', '081234567890', 'Keluarga', '1'],
        ['Ibu Rina Marlina', '', 'Kantor', ''],
        ['Keluarga Besar Hartono', '6281298765432', '', 'A3'],
    ];

    /** Columns (0-based) stored as Text: WhatsApp and Meja. */
    private const TEXT_COLUMNS = [1, 3];

    /** Writes the workbook to a temporary file and returns its path. */
    public function build(): string
    {
        $path = tempnam(sys_get_temp_dir(), 'tamu');
        $zip = new ZipArchive;
        $zip->open($path, ZipArchive::OVERWRITE);

        $zip->addFromString('[Content_Types].xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            .'<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            .'<Default Extension="xml" ContentType="application/xml"/>'
            .'<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            .'<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>'
            .'<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            .'</Types>');
        $zip->addFromString('_rels/.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            .'</Relationships>');
        $zip->addFromString('xl/workbook.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            .'<sheets><sheet name="Daftar Tamu" sheetId="1" r:id="rId1"/></sheets></workbook>');
        $zip->addFromString('xl/_rels/workbook.xml.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            .'<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>'
            .'<Relationship Id="rId2" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>'
            .'</Relationships>');
        // Cell style 1 = Text (numFmt 49), cell style 2 = bold header.
        $zip->addFromString('xl/styles.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            .'<fonts count="2"><font><sz val="11"/><name val="Calibri"/></font><font><b/><sz val="11"/><name val="Calibri"/></font></fonts>'
            .'<fills count="2"><fill><patternFill patternType="none"/></fill><fill><patternFill patternType="gray125"/></fill></fills>'
            .'<borders count="1"><border><left/><right/><top/><bottom/><diagonal/></border></borders>'
            .'<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            .'<cellXfs count="3"><xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>'
            .'<xf numFmtId="49" fontId="0" fillId="0" borderId="0" xfId="0" applyNumberFormat="1"/>'
            .'<xf numFmtId="0" fontId="1" fillId="0" borderId="0" xfId="0" applyFont="1"/></cellXfs>'
            .'<cellStyles count="1"><cellStyle name="Normal" xfId="0" builtinId="0"/></cellStyles>'
            .'</styleSheet>');
        $zip->addFromString('xl/worksheets/sheet1.xml', $this->sheet());
        $zip->close();

        return $path;
    }

    private function sheet(): string
    {
        $rows = '';
        foreach ([self::HEADER, ...self::SAMPLES] as $r => $values) {
            $cells = '';
            foreach ($values as $c => $value) {
                $style = $r === 0 ? 2 : (in_array($c, self::TEXT_COLUMNS, true) ? 1 : 0);
                $cells .= '<c r="'.chr(65 + $c).($r + 1).'" s="'.$style.'" t="inlineStr"><is><t>'.htmlspecialchars($value, ENT_XML1).'</t></is></c>';
            }
            $rows .= '<row r="'.($r + 1).'">'.$cells.'</row>';
        }

        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            .'<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
            .'<cols><col min="1" max="1" width="32" customWidth="1"/>'
            .'<col min="2" max="2" width="20" style="1" customWidth="1"/>'
            .'<col min="3" max="3" width="16" customWidth="1"/>'
            .'<col min="4" max="4" width="10" style="1" customWidth="1"/></cols>'
            .'<sheetData>'.$rows.'</sheetData></worksheet>';
    }
}
