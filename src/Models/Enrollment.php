<?php

namespace Suru\Docebo\DoceboApiWrapper\Models;

class Enrollment extends Model {

  protected $dates = [
    'date_enrollment',
    'date_first_access',
    'date_complete',
    'date_complete',
    'active_from',
    'active_until'
  ];

  protected $id_user;
  protected $username;
  protected $id_course;
  protected $course_code;
  protected $level;
  protected $status;
  protected $date_enrollment;
  protected $date_first_access;
  protected $date_complete;
  protected $date_last_access;
  protected $active_from;
  protected $active_until;
  protected $first_name;
  protected $last_name;
  protected $email;
  protected $course_link;
  protected $course_name;
  protected $course_fields;
    
}

