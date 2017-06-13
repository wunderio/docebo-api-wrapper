<?php

namespace Suru\Docebo\DoceboApiWrapper\Models;

class LearningPlanEnrollment extends Model {

  protected $dates = [
    'subscription_date',
    'complete_date',
  ];

  protected $id_path;
  protected $path_code;
  protected $id_user;
  protected $first_name;
  protected $last_name;
  protected $status;
  protected $percentage;
    
}

