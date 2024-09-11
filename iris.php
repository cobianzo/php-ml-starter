<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/helpers.php';


$csv_file = __DIR__ . '/data/iris.csv';
$number_input_columns = 4; // for the preprocess of data, columns as inputs in the csv.
$number_of_types = 3; // classification 1/2/3

// This script of classification works with the type of qualitiy of the wines too.
// $csv_file = __DIR__ . '/data/wine.csv';
// $number_input_columns = 13;
// $number_of_types = 3; // classification 1/2/3

Helpers::show_script_info( __FILE__, $csv_file );


/**
 * We select the Model: preprocessing method, training method, and evaluation method
 * ===============================================================================
 */
  
$methods = new My_Model(
  'StratifiedRandomSplit',
  '\Phpml\Classification\KNearestNeighbors',
  '\Phpml\Metric\Accuracy\score'
);

$methods->show();
if ( ! $methods->check() ) {
  Helpers::output('Error: Some classes or methods do not exist.' . PHP_EOL);
  exit( 1 );
}

/** =============================================================================== */

/**
 * Simple Machile Learning exercise:
 * dataset:  insurance.csv - a more or less linear dataset. The bigger the X, bigger the Y.
 * 
 * Run it with: `php insurance.php`
 * 
 * 1. Fetch data
 * 2. Preprocess data: split into training and test sets
 * 3. Train model with a subset of data of known solutions
 * 4. Predict: model on the rest of data
 * 5. Evaluate how good we did
 * 6. Predict again of a completely new value.
 */


use Phpml\Dataset;


$data = new Dataset\CsvDataset( $csv_file, $number_input_columns, true );
// error control checking if Data is loaded ok.



// 1. Preprocess data, retrieve the training and test sets
// =========================================================
$class = $methods->preprocess_namespace .  $methods->PREPROCESS;

$dataset = new $class( $data, 0.2 ); // $dataset = new \Phpml\CrossValidation\RandomSplit( $data, 0.2 );
$blocks_of_data = [ 
  $dataset->getTrainSamples(),
  $dataset->getTrainLabels(),
  $dataset->getTestSamples(),
  $dataset->getTestLabels()
];
[$trainsamples, $trainlabels, $knownsamples, $knownlabels] =  $blocks_of_data;

// Create THE MODEL! 
$class = $methods->train_namespace . $methods->TRAIN;
$model = new $class( 3 ); // classification in 3 types

// 2. good. train it!
// =========================================================
// trained based on samples and labels. The model is now ready to make predictions.
// Helpers::output( PHP_EOL . PHP_EOL . 'TRAINING===='.PHP_EOL .'===== '.PHP_EOL .'');
// Helpers::output('Samples inputs' . PHP_EOL);
// dd($trainsamples);
// Helpers::output('Classified as ' . PHP_EOL);
// dd($trainlabels);
$model->train( $trainsamples, $trainlabels );


// 3. Run a prediction in the rest of sample data
// =========================================================
// Now we are ready to predict 
$predict = $model->predict( $knownsamples );
// foreach ( $predict as $single_prediction ) {
//   Helpers::output($single_prediction . PHP_EOL);
// }

// 4. Evaluate the accuracy in two ways
// =========================================================
$accuracy = $methods->evaluate_class::{$methods->EVALUATE}( $knownlabels, $predict ); // Accuracy

// === just output stuff ===
// =========================================================
// =========================================================
Helpers::output("<hr/>");
Helpers::output("Accuracy: $accuracy " . PHP_EOL);