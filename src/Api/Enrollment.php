<?php

namespace Suru\Docebo\DoceboApiWrapper\Api;

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
    $response = $this->master->call($this->endpoints['list'], $headers, $parameters);
    
    return $response->body;
  }

}
