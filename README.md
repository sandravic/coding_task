# coding_task
Build a Supplier Product List Processor

Application Overview
This PHP application is designed to process files in three different formats: CSV, TSV, JSON, and XML. It reads input data from these files, validates certain required fields, and then calculates and saves the count of unique combinations of attributes based on the data provided in the input file as CSV file

Requirements 
•	PHP installed on your system (version 7.0 or higher).
•	Command-line interface (CLI) access.

Usage: 
1. Save the provided PHP code in a file named `processor.php`.

2. Open a command-line terminal.

3. Navigate to the directory where you saved ` processor.php`.

4. To run the application, use the following command-line syntax:

    php processor.php --file input.(csv|json|xml|tsv)

    Replace `input.(csv|json|xml|tsv)` with the actual path to the input file you want to process. The input file should be in one of the supported formats: CSV, JSON, TSV or XML.
Validation Requirements
The application requires the presence of the "brand name" and "model name" fields in the input data.
The validation of required fields is case-insensitive and accepts names starting with "brand" or "model" (e.g., "BrandName", "modelName", "brand_new", "ModelA").
If the required "brand name" or "model name" fields are missing or not valid, the application will display an error message.



Example:
Suppose you have a CSV file named `products.csv` in the same directory as the `processor.php` file. To process this file and calculate unique combination counts, you would run:
php processor.php --file files/product_details.csv
or 
php processor.php --file files/ products_tab_separated.tsv
or
php processor.php --file files/products_json_file.json
or
php processor.php --file files/products_xml_file.xml

The application will generate an output file named `product_details_combination_count.csv`/
`products_tab_separated_combination_count.csv`/
`products_json_file_combination_count.csv`/
`products_xml_file_combination_count.csv` with the calculated counts of unique combinations of attributes in folder “files”.

Notes:
•	The application automatically detects the file format based on the extension (csv, json, tsv or xml).
•	If the provided file format is not supported, the application will display an "Unsupported file format." message.
•	Make sure the input file is correctly formatted and contains the necessary headers and data.
•	Remember to adjust file paths and filenames as necessary based on your actual setup.
Unit Testing
1.Install PHPUnit if you haven't already using Composer:
composer require --dev phpunit/phpunit
2. Save the provided unit test code in a file named ` ProcessorTest.php`.
3. Run
vendor/bin/phpunit test/ProcessorTest.php

