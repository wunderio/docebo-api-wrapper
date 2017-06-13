<?php

namespace Suru\Docebo\DoceboApiWrapper\Api;
use Suru\Docebo\DoceboApiWrapper\Models\Enrollment as ModelEnrollment;

class Enrollment extends BaseEndpoint {

  /**
   * Enrollment endpoints.
   *
   * @var array
   */
  protected $endpoints = [
    'list'  => '/api/enroll/enrollments',
  ];

  /**
   * List enrollments, filtered by user-defined criteria.
   *
   * Possible parameters:
   *
   * id_user (integer, optional): numeric ID of an user
   * id_course (string, optional): nuemric ID of a course
   * updated_from (string, optional): additional filter on enrollments, date filter on "last_access" property
   * user_ext_type (string, optional): external user type
   * user_ext (string, optional): external user ID
   * from (integer, optional): limit the resultset by record offset
   * count (integer, optional): limit the resultset by number of records
   * start_date (string, optional): limit the resultset by date (yyyy-MM-dd HH:mm:ss format, UTC timezone)
   * end_date (string, optional): limit the resultset by date (yyyy-MM-dd HH:mm:ss format, UTC timezone)
   * username (string, optional): limit the resultset by username
   * completed_from (string, optional): the start date & time used to filter enrollments based on the completion date (in yyyy-MM-dd HH:mm:ss format, UTC timezone)
   * completed_to (string, optional): the end date & time used to filter enrol
   *
   * @param array $parameters
   * @return array
   */
  public function getEnrollments($parameters = []) {
    $headers = $this->master->getAccessTokenHeader();
    $enrollments = [];
    $response = $this->master->call($this->endpoints['list'], $headers, $parameters);
    foreach ($response->body->enrollments as $enrollment) {
      $enrollments[] = new ModelEnrollment((array) $enrollment);
    }

    return $enrollments;
  }

}
