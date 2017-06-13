<?php

namespace Suru\Docebo\DoceboApiWrapper\Api;
use Suru\Docebo\DoceboApiWrapper\Models\LearningPlan as ModelLearningPlan;
use Suru\Docebo\DoceboApiWrapper\Models\LearningPlanEnrollment as ModelLearningPlanEnrollment;


class LearningPlan extends BaseEndpoint {

  /**
   * LearningPlan endpoints.
   *
   * @var array
   */
  protected $endpoints = [
    'list'  => '/api/learningplan/listPlans',
    'list_enrollments' => '/api/learningplan/enrollments'
  ];

  /**
   * List learning plans
   *
   * Possible parameters:
   *
   * from (integer, optional): (default=false) the offset to use for the pagination (starts from 0) ,
   * count (integer, optional): (default=false) the number of records to return per each call. If passed as false, all records are returned.
   *
   * @param array $parameters
   * @return array
   */
  public function getLearningPlans($parameters = []) {
    $headers = $this->master->getAccessTokenHeader();
    $learningPlans = [];
    $response = $this->master->call($this->endpoints['list'], $headers, $parameters);
    foreach ($response->body->learning_plans as $learning_plan) {
      $learningPlans[] = new ModelLearningPlan((array) $learning_plan);
    }

    return $learningPlans;
  }

  /**
   * List learning plan enrollments, filtered by user-defined criteria.
   *
   * Possible parameters:
   *
   * from (integer, optional): (default=false) the offset to use for the pagination (starts from 0).
   * count (integer, optional): (default=false) the number of records to return per each call. If passed as false, all records are returned.
   * ids_path (string, optional): comma separated list of learning plan IDs, needed to filter the results.
   * path_codes (string, optional): comma separated list of learning plan codes, needed to filter the results. This param is ignored if id_paths is passed non empty.
   * user_ids (string, optional): comma separated user ids, needed to filter the results.
   *
   * @param array $parameters
   * @return array
   */
  public function getLearningPlanEnrollments($parameters = []) {
    $headers = $this->master->getAccessTokenHeader();
    $enrollments = [];
    $response = $this->master->call($this->endpoints['list_enrollments'], $headers, $parameters);
    foreach ($response->body->learning_plans as $enrollment) {
      $enrollments[] = new ModelLearningPlanEnrollment((array) $enrollment);
    }

    return $enrollments;
  }

}
