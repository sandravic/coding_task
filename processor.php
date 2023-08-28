<?php
class Product {
    private $attributes = []; // Associative array to store dynamic attributes and values

    public function setAttribute($name, $value) {
        $normalizedAttributeName = $this->normalizeAttributeName($name);
        $this->attributes[$normalizedAttributeName] = $value;
    }

    public function getAttribute($name) {
        $normalizedAttributeName = $this->normalizeAttributeName($name);
        return $this->attributes[$normalizedAttributeName] ?? null;
    }

    public function getAttributes() {
        return $this->attributes;
    }
   // Validation
    public function validateRequiredFields() {
        $requiredFieldFound = false;

        foreach (['make','brand', 'model'] as $requiredField) {
            if (!empty($this->getAttribute($requiredField))) {
                $requiredFieldFound = true;
                break;
            }
        }

        if (!$requiredFieldFound) {
            throw new Exception("Either 'make' or 'brand' field must be present.");
        }

        if (empty($this->getAttribute('model'))) {
            throw new Exception("Model field cannot be empty.");
        }
    }
    private function normalizeAttributeName($name) {
        $normalized = str_replace(['_', ' '], '', strtolower($name));
        if (str_starts_with($normalized, 'brand')) {
            return 'brand';
        } elseif (str_starts_with($normalized, 'make')) {
            return 'make';
        } elseif (str_starts_with($normalized, 'model')) {
            return 'model';
        }
        return $normalized;
    }
}


class Processor {
    
    private static function getCombinationKey($product) {
        $attributes = $product->getAttributes();
        $parts = array_keys($attributes);
        return implode('-', array_map(fn($part) => str_replace('-', ' ', $attributes[$part] ?? ''), $parts));
    }

    private static function saveUniqueCombinations($combinations, $outputFile, $headers) {
        $file = fopen($outputFile, 'w');
        if (!$file) {
            throw new Exception("Unable to create the output file.");
        }

        $headers[] = 'count';
        fputcsv($file, $headers);

        foreach ($combinations as $combination => $count) {
            $fields = explode('-', $combination);
            $fields[] = $count;
            fputcsv($file, $fields);
        }

        fclose($file);
    }
     // Processes a CSV file containing product data.
     public static function processCSV($filename, $outputFile) {
        $file = fopen($filename, 'r');
        if (!$file) {
            throw new Exception("Unable to open the file.");
        }
    
       
        $headers = array_map('trim', fgetcsv($file)); // Read and trim headers from the first line

        $uniqueCombinations = [];
    
        while (($data = fgetcsv($file)) !== false) {
            $product = new Product();
            foreach ($headers as $index => $header) {
                $product->setAttribute(trim($header, "\xEF\xBB\xBF"), $data[$index]);
            }
    
            try {
                $product->validateRequiredFields();
                $combinationKey = self::getCombinationKey($product);
                $uniqueCombinations[$combinationKey] = isset($uniqueCombinations[$combinationKey]) ? $uniqueCombinations[$combinationKey] + 1 : 1;
            } catch (Exception $e) {
                echo "Validation Error: " . $e->getMessage() . "\n";
            }
        }
    
        fclose($file);
    
        self::saveUniqueCombinations($uniqueCombinations, $outputFile, $headers);
    }
    // Processes a json file containing product data.
    public static function processJSON($filename, $outputFile) {
        $jsonContents = file_get_contents($filename);
        $jsonData = json_decode($jsonContents, true);

        if (!$jsonData) {
            throw new Exception("Error decoding JSON data.");
        }

        $uniqueCombinations = [];
        $headers = array_keys($jsonData[0]); // Assuming the first item has all headers

        foreach ($jsonData as $item) {
            $product = new Product();
            foreach ($headers as $header) {
                $product->setAttribute(trim($header, "\xEF\xBB\xBF"), $item[$header]);
            }

            try {
                $product->validateRequiredFields();
                $combinationKey = self::getCombinationKey($product);

                $uniqueCombinations[$combinationKey] = isset($uniqueCombinations[$combinationKey]) ? $uniqueCombinations[$combinationKey] + 1 : 1;
            } catch (Exception $e) {
                echo "Validation Error: " . $e->getMessage() . "\n";
            }
         }

        self::saveUniqueCombinations($uniqueCombinations, $outputFile, $headers);
    }

    // Processes a tsv file containing product data.
    public static function processTSV($filename, $outputFile) {
        $file = fopen($filename, 'r');
        if (!$file) {
            throw new Exception("Unable to open the file.");
        }
    
        $headers = fgetcsv($file, 0, "\t"); // Read headers from the first line
        $headers = array_map('trim', $headers);
        $uniqueCombinations = [];
    
        while (($data = fgetcsv($file, 0, "\t")) !== false) {
            $product = new Product();
            foreach ($headers as $index => $header) {
                // Use the correct index ($index) to access data
                $productData = $data[$index] ?? null; // Use $data instead of $item
                $product->setAttribute(trim($header, "\xEF\xBB\xBF"), $productData);
            }
    
            try {
                $product->validateRequiredFields();
    
                $combinationKey = self::getCombinationKey($product);
    
                $uniqueCombinations[$combinationKey] = isset($uniqueCombinations[$combinationKey]) ? $uniqueCombinations[$combinationKey] + 1 : 1;
            } catch (Exception $e) {
                echo "Validation Error: " . $e->getMessage() . "\n";
            }
        }
    
        fclose($file);
    
        self::saveUniqueCombinations($uniqueCombinations, $outputFile, $headers);
    }
    

    // Processes an XML file containing product data.
    public static function processXML($filename, $outputFile) {
        $xmlData = simplexml_load_file($filename);
        if (!$xmlData) {
            throw new Exception("Error loading XML data.");
        }

        $uniqueCombinations = [];
        $headers = array_keys((array)$xmlData->row[0]); // Assuming the first row has all headers

        foreach ($xmlData->row as $item) {
            $product = new Product();
            foreach ($headers as $header) {
                $attributeValue = (string)$item->$header; // Convert to string to ensure consistent type
               
        
                $product->setAttribute(trim($header, "\xEF\xBB\xBF"), $attributeValue);
            }

            try {
                $product->validateRequiredFields();

                $combinationKey = self::getCombinationKey($product);

                $uniqueCombinations[$combinationKey] = isset($uniqueCombinations[$combinationKey]) ? $uniqueCombinations[$combinationKey] + 1 : 1;
            } catch (Exception $e) {
                echo "Validation Error: " . $e->getMessage() . "\n";
            }  
        }

        self::saveUniqueCombinations($uniqueCombinations, $outputFile, $headers);
    }
}

class FileProcessor {
    public static function processFile($filename) {
        $fileExtension = pathinfo($filename, PATHINFO_EXTENSION);

        switch ($fileExtension) {
            case 'csv':
                $outputFile = str_replace('.csv', '_combination_count.csv', $filename);
                Processor::processCSV($filename, $outputFile);
                break;
            case 'json':
                $outputFile = str_replace('.json', '_combination_count.csv', $filename);
                Processor::processJSON($filename, $outputFile);
                break;
            case 'xml':
                $outputFile = str_replace('.xml', '_combination_count.csv', $filename);
                Processor::processXML($filename, $outputFile);
                break;
            case 'tsv':
                $outputFile = str_replace('.tsv', '_combination_count.csv', $filename);
                Processor::processTSV($filename, $outputFile);
                break;
            default:
                echo "Unsupported file format.\n";
        }
    }
}

if (php_sapi_name() === 'cli') {
    $shortOptions = "f:"; // Short option for --file
    $longOptions = ['file:'];
    $args = getopt($shortOptions, $longOptions);
    
    if (isset($args['file'])) {
        $filename = $args['file'];
        FileProcessor::processFile($filename);
    } else {
        echo "Usage: php parser.php --file input.(csv|json|xml|tsv)\n";
    }
}

?>
