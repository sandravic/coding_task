<?php

use PHPUnit\Framework\TestCase;

require_once 'vendor/autoload.php'; // Include the Composer autoloader

class ProcessorTest extends TestCase {
    
    public function testProcessCSV() {
        $testCSVFile = '/Users/sandravictor/coding_task/test/test_files/test.csv';
        $testOutputFile = '/Users/sandravictor/coding_task/test/test_files/output.csv';
        $headers = ['make', 'model', 'colour', 'conditions', 'grade', 'capacity', 'network'];

        // Create a mock CSV file with test data
        $csvTestData = [
            ['make', 'model', 'colour', 'conditions', 'grade', 'capacity', 'network'],
            ['Toyota', 'Camry', 'Red', 'Good', 'A', '5', '4G'],
            ['Honda', 'Civic', 'Blue', 'Excellent', 'B', '4', '3G'],
            // Add more rows as needed
        ];

        $csvFile = fopen($testCSVFile, 'w');
        foreach ($csvTestData as $row) {
            fputcsv($csvFile, $row);
        }
        fclose($csvFile);

        Processor::processCSV($testCSVFile, $testOutputFile, $headers);

        $this->assertFileExists($testOutputFile);
        // Add more assertions as needed
    }

    public function testProcessJSON() {
        $testJSONFile = '/Users/sandravictor/coding_task/test/test_files/test_json_file.json';
        $testOutputFile = '/Users/sandravictor/coding_task/test/test_files/test_json_output.csv';

        // Create a mock JSON file with test data
        $jsonTestData = [
            ['make' => 'Toyota', 'model' => 'Camry', 'colour' => 'Red'],
            ['make' => 'Honda', 'model' => 'Civic', 'colour' => 'Blue'],
            // Add more objects as needed
        ];

        file_put_contents($testJSONFile, json_encode($jsonTestData));

        Processor::processJSON($testJSONFile, $testOutputFile);

        $this->assertFileExists($testOutputFile);
        // Add more assertions as needed
    }

    public function testProcessXML() {
        $testXMLFile = '/Users/sandravictor/coding_task/test/test_files/test_xml_file.xml';
        $testOutputFile = '/Users/sandravictor/coding_task/test/test_files/test_xml_output.csv';

        // Create a mock XML file with test data
        $xmlTestData = '<?xml version="1.0" encoding="UTF-8"?>
        <data>
            <entry>
                <make>Toyota</make>
                <model>Camry</model>
                <colour>Red</colour>
            </entry>
            <entry>
                <make>Honda</make>
                <model>Civic</model>
                <colour>Blue</colour>
            </entry>
            <!-- Add more entries as needed -->
        </data>';

        file_put_contents($testXMLFile, $xmlTestData);

        Processor::processXML($testXMLFile, $testOutputFile);

        $this->assertFileExists($testOutputFile);
        // Add more assertions as needed
    }
    public function testProcessTSV() {
        $testTSVFile = '/Users/sandravictor/coding_task/test/test_files/test.tsv';
        $testOutputFile = '/Users/sandravictor/coding_task/test/test_files/test_tsv_output.csv';
        $headers = ['make', 'model', 'colour']; // Adjust headers as needed

        // Create a mock TSV file with test data
        $tsvTestData = [
            implode("\t", ['make', 'model', 'colour']), // Headers
            implode("\t", ['Toyota', 'Camry', 'Red']),
            implode("\t", ['Honda', 'Civic', 'Blue']),
            // Add more rows as needed
        ];

        file_put_contents($testTSVFile, implode("\n", $tsvTestData));

        Processor::processTSV($testTSVFile, $testOutputFile, $headers);

        $this->assertFileExists($testOutputFile);
        // Add more assertions as needed
    }
    
    // Add additional test cases as needed
}
