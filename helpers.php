<?php 

class My_Model {

  public $preprocess_namespace = '\Phpml\CrossValidation\\';
  public $PREPROCESS;
  public $train_namespace      = '\Phpml\Regression\\';
  public $TRAIN;
  public $evaluate_class       = '\Phpml\Metric\\Regression';
  public $EVALUATE;
  

  /**
   *  Initializes a new instance of the My_Model class.
   * 
   *  @param string $preprocess The name of the preprocessing method to use. Defaults to 'RandomSplit'.
   *  @param string $train      The name of the training method to use. Defaults to 'LeastSquares'.
   *  @param string $evaluate   The name of the evaluation method to use. Defaults to 'r2Score'.
   * 
   *  @return void
   */
  public function __construct( string $preprocess = '', string $train = '', string $evaluate = '' ) {

      $this->PREPROCESS = empty($preprocess) ? 'RandomSplit'  : $preprocess;
      if ( false !== strpos( $preprocess, '\\') ) {
        $this->PREPROCESS           = substr($preprocess, strrpos($preprocess, '\\') + 1);
        $this->preprocess_namespace = substr($preprocess, 0, strrpos($preprocess, '\\') + 1);
      }

      $this->TRAIN      = empty($train)      ? 'LeastSquares' : $train;
      if ( false !== strpos( $train, '\\') ) {
        $this->TRAIN           = substr($train, strrpos($train, '\\') + 1);
        $this->train_namespace = substr($train, 0, strrpos($train, '\\') + 1);
      }

      $this->EVALUATE   = empty($evaluate)   ? 'r2Score'      : $evaluate;
      if ( false !== strpos( $evaluate, '\\') ) {
        // capture all text before and after the last '/'.
        // eg , from \Phpml\Metric\Accuracy\score save 'score' as the name of the method.
        $this->EVALUATE       = substr($evaluate, strrpos($evaluate, '\\') + 1);
        $this->evaluate_class = substr($evaluate, 0, strrpos($evaluate, '\\'));
      }
  }

  /**
   *  Displays all properties of the current object.
   * 
   *  @return void
   */
  public function show() {
    // for every property, show the name of the property and the value
    Helpers::output("<h3>Model created from the following:</h3>");
    Helpers::output("<hr/>");
    foreach ($this as $key => $value) {
      Helpers::output( ' --- ' . $key . ': ' . $value . PHP_EOL );
    }
    Helpers::output("<hr/>");
  }

  /**
   *  Checks if the preprocessing, training, and evaluation classes and methods exist.
   *
   *  @throws \Exception if any of the classes or methods do not exist
   *  @return bool true if all classes and methods exist, false otherwise
   */
  public function check() {
    $class = $this->preprocess_namespace .  $this->PREPROCESS;
    $ok    = true;
    if ( ! class_exists( $class ) ) {
      throw new \Exception( '::::>>> Preprocessing class not found: ' . $class );
      $ok = false;
    }

    $class = $this->train_namespace . $this->TRAIN;
    if ( ! class_exists( $class ) ) {
      throw new \Exception( '::::>>> Training class not found: ' . $class );
      $ok = false;
    }
    
    if ( ! class_exists( $this->evaluate_class ) ) {
      throw new \Exception( '::::>>> Evaluation class not found: ' . $this->evaluate_class );
      $ok = false;
    }
    // check if the static method exists in class 
    if ( ! method_exists( $this->evaluate_class, $this->EVALUATE ) ) {
      throw new \Exception( '::::>>> Evaluation method not found: ' . $this->EVALUATE );
      $ok = false;
    }
    return $ok;
  }
  
  // create a generic setter for all propierties
  // Usage: $mi_instance->set('PREPROCESS', 'StratifiedRandomSplit');
  public function __set( string $name, string $value ) : void
  {
      $this->$name = $value;
  }
}


function dd( $car = '' ) {
  echo '<pre>';
  print_r( $car );
  echo '</pre>';
} 

function ddie( $car = '' ) {
  dd($car);
  exit;
} 


class Helpers {
  public static function output( $message ) {
    // check if the environment is console or web
    if ( php_sapi_name() === 'cli' ) {
      // replace '<hr/>' with '====='
      $separator = PHP_EOL . '------------' . PHP_EOL;
      $message = str_replace( '<hr />', $separator, $message );
      $message = str_replace( '<hr/>', $separator, $message );
      echo strip_tags( $message );
    } else {
      // replace PHP_EOL with <br/> 

      echo nl2br( $message );
    }
  }

  public static function show_script_info( string $file, string $csv_filename ) : void {
    // Get the name of this script file
    $script_name = basename( $file );
    Helpers::output( PHP_EOL . '<hr/>' );
    Helpers::output("<h2> $script_name </h2>");
    Helpers::output( '<hr/>' );

    // Get the name of this script file
    Helpers::output( "Data: <b> $csv_filename </b>"  );
    Helpers::output(  '<hr/>' );
  }
}