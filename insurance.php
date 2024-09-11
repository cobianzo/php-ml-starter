<?php 
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/helpers.php';


$csv_insurance = __DIR__ . '/data/insurance.csv';
Helpers::show_script_info( __FILE__, $csv_insurance );

/**
 * We select the Model: preprocessing method, training method, and evaluation method
 * ===============================================================================
 */
if ( ! isset( $methods ) ) {
  $methods = new My_Model(
    'RandomSplit',
    'LeastSquares',
    'r2Score'
  );

  // This model works very bad on insurance data
  $methods = new My_Model(
    'StratifiedRandomSplit',
    'SVR',
    'score'
  );
  $methods->evaluate_class = '\Phpml\Metric\Accuracy';
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


$data = new Dataset\CsvDataset( $csv_insurance, true );

// 1. Preprocess data, retrieve the training and test sets
// =========================================================
$class = $methods->preprocess_namespace .  $methods->PREPROCESS;

$dataset = new $class( $data, 0.5 ); // $dataset = new \Phpml\CrossValidation\RandomSplit( $data, 0.2 );
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
try {
  $regression->train( $trainsamples, $trainlabels );
} catch (Exception $e) {
  die($e->getMessage());
}
// 3. Run a prediction in the rest of sample data
// =========================================================
// Now we are ready to predict 
$predict = $regression->predict( $knownsamples );

// 4. Evaluate the accuracy
// =========================================================
$accuracy = $methods->evaluate_class::{$methods->EVALUATE}( $knownlabels, $predict );

// === just output stuff ===
// =========================================================
// =========================================================
Helpers::output( "Accuracy: $accuracy " . PHP_EOL );
$predict = $regression->predict( [80] );
Helpers::output( "Prediction on 80: $predict"  . PHP_EOL );