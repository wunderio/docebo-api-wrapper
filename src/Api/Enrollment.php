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
   * Get enrollments by course.
   *
   * @param int $course_id
   * @return array
   */
  public function getEnrollments($course_id = NULL) {
    $headers = $this->master->getAccessTokenHeader();
    $parameters = [
      'id_course' => $course_id,
    ];
    $enrollments = [];
    $response = $this->master->call($this->endpoints['list'], $headers, $parameters);
    foreach ($response->body->enrollments as $enrollment) {
      $enrollments[] = new ModelEnrollment((array) $enrollment);
    }

    return $enrollments;
  }

}
