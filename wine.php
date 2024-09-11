<?php 
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/helpers.php';

$csv_file = __DIR__ . '/data/wine.csv';
Helpers::show_script_info( __FILE__, $csv_file );

/**
 * We select the Model: preprocessing method, training method, and evaluation method
 * ===============================================================================
 */
if ( ! isset( $methods ) ) {
  // This model works very bad on insurance data
  $methods = new My_Model(
    'StratifiedRandomSplit',
    'SVR',
    '\Phpml\Metric\Accuracy\score'
  );
}

$methods->show();
if ( ! $methods->check() ) {
  Helpers::output( 'Error: Some classes or methods do not exist.' . PHP_EOL );
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


$data = new Dataset\CsvDataset( $csv_file, 13, true );
// error control checking if Data is loaded ok.



// 1. Preprocess data, retrieve the training and test sets
// =========================================================
$class = $methods->preprocess_namespace .  $methods->PREPROCESS;

$dataset = new $class( $data, 0.2, 150 ); // $dataset = new \Phpml\CrossValidation\RandomSplit( $data, 0.2 );
$blocks_of_data = [ 
  $dataset->getTrainSamples(),
  $dataset->getTrainLabels(),
  $dataset->getTestSamples(),
  $dataset->getTestLabels()
];
[$trainsamples, $trainlabels, $knownsamples, $knownlabels] =  $blocks_of_data;

// Create a LeastSquares regression model
$class = $methods->train_namespace . $methods->TRAIN;
$regression = new $class(); // $regression = new \Phpml\Regression\LeastSquares();

// 2. good. train it!
// =========================================================
// trained based on samples and labels. The model is now ready to make predictions.
$regression->train( $trainsamples, $trainlabels );

// 3. Run a prediction in the rest of sample data
// =========================================================
// Now we are ready to predict 
$predict = $regression->predict( $knownsamples );

// round all values of predicted. Otherwise it fails.
$predict = array_map( 'round', $predict );

// 4. Evaluate the accuracy in two ways
// =========================================================
$accuracyR2S = \Phpml\Metric\Regression::r2Score( $knownlabels, $predict ); // worse
$accuracy = $methods->evaluate_class::{$methods->EVALUATE}( $knownlabels, $predict ); // better

// === just output stuff ===
// =========================================================
// =========================================================
Helpers::output( "<hr/>" );
Helpers::output( "Accuracy R2Score: $accuracyR2S " . PHP_EOL );
Helpers::output( "Accuracy Good: $accuracy " . PHP_EOL );
$predict = $regression->predict( [80] );