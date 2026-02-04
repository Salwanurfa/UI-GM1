<?php

/**
 * Excel Export Helper
 * Simple Excel export without external library
 * Uses HTML table format that Excel can open
 */

if (!function_exists('exportToExcel')) {
    /**
     * Export data to Excel format (HTML table)
     * 
     * @param array $data Data to export
     * @param array $headers Column headers
     * @param string $filename Filename without extension
     * @param string $title Sheet title
     */
    function exportToExcel($data, $headers, $filename = 'export', $title = 'Data Export')
    {
        // Set headers for Excel download
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
        header('Cache-Control: max-age=0');
        
        // Start HTML
        echo '<!DOCTYPE html>';
        echo '<html>';
        echo '<head>';
        echo '<meta charset="UTF-8">';
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
        echo '<style>';
        echo 'table { border-collapse: collapse; width: 100%; }';
        echo 'th, td { border: 1px solid #000; padding: 8px; text-align: left; }';
        echo 'th { background-color: #4472C4; color: white; font-weight: bold; }';
        echo '.title { font-size: 18px; font-weight: bold; margin-bottom: 10px; }';
        echo '.number { mso-number-format:"0\.00"; }';
        echo '.currency { mso-number-format:"Rp\\ \#\,\#\#0"; }';
        echo '.date { mso-number-format:"dd/mm/yyyy"; }';
        echo '</style>';
        echo '</head>';
        echo '<body>';
        
        // Title
        echo '<div class="title">' . htmlspecialchars($title) . '</div>';
        echo '<div style="margin-bottom: 20px;">Dicetak pada: ' . date('d/m/Y H:i:s') . '</div>';
        
        // Table
        echo '<table>';
        
        // Headers
        echo '<thead><tr>';
        foreach ($headers as $header) {
            echo '<th>' . htmlspecialchars($header) . '</th>';
        }
        echo '</tr></thead>';
        
        // Data
        echo '<tbody>';
        if (!empty($data)) {
            foreach ($data as $row) {
                echo '<tr>';
                foreach ($row as $key => $cell) {
                    // Detect cell type
                    $class = '';
                    if (is_numeric($cell) && strpos($key, 'harga') !== false || strpos($key, 'nilai') !== false) {
                        $class = 'currency';
                    } elseif (is_numeric($cell) && (strpos($key, 'berat') !== false || strpos($key, 'jumlah') !== false)) {
                        $class = 'number';
                    } elseif (strpos($key, 'tanggal') !== false || strpos($key, 'date') !== false) {
                        $class = 'date';
                    }
                    
                    echo '<td class="' . $class . '">' . htmlspecialchars($cell) . '</td>';
                }
                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan="' . count($headers) . '" style="text-align: center;">Tidak ada data</td></tr>';
        }
        echo '</tbody>';
        
        echo '</table>';
        echo '</body>';
        echo '</html>';
        
        exit;
    }
}

if (!function_exists('exportToCSV')) {
    /**
     * Export data to CSV format
     * 
     * @param array $data Data to export
     * @param array $headers Column headers
     * @param string $filename Filename without extension
     */
    function exportToCSV($data, $headers, $filename = 'export')
    {
        // Set headers for CSV download
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment;filename="' . $filename . '.csv"');
        header('Cache-Control: max-age=0');
        
        // Open output stream
        $output = fopen('php://output', 'w');
        
        // Add BOM for UTF-8
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Write headers
        fputcsv($output, $headers);
        
        // Write data
        if (!empty($data)) {
            foreach ($data as $row) {
                fputcsv($output, $row);
            }
        }
        
        fclose($output);
        exit;
    }
}

if (!function_exists('formatExcelNumber')) {
    /**
     * Format number for Excel
     */
    function formatExcelNumber($number, $decimals = 2)
    {
        return number_format($number, $decimals, '.', '');
    }
}

if (!function_exists('formatExcelCurrency')) {
    /**
     * Format currency for Excel
     */
    function formatExcelCurrency($amount)
    {
        return number_format($amount, 0, '', '');
    }
}

if (!function_exists('formatExcelDate')) {
    /**
     * Format date for Excel
     */
    function formatExcelDate($date, $format = 'd/m/Y')
    {
        if (empty($date)) return '';
        
        try {
            $dt = new DateTime($date);
            return $dt->format($format);
        } catch (Exception $e) {
            return $date;
        }
    }
}
