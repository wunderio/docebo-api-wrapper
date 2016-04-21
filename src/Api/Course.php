<?php

namespace Suru\Docebo\DoceboApiWrapper\Api;

use Suru\Docebo\DoceboApiWrapper\Api\BaseEndpoint;
use Suru\Docebo\DoceboApiWrapper\Models\Course as ModelCourse;

class Course extends BaseEndpoint {

    /**
     * Course endpoints.
     *
     * @var array
     */
    protected $endpoints = [
        'list'  => '/api/course/courses',
    ];

    /**
     * Get courses.
     *
     * @return array
     */
    public function getCourseList()
    {
		$headers = $this->master->getAccessTokenHeader();

		$parameters = [];

		$response = $this->master->call($this->endpoints['list'], $headers, $parameters);

        $courses = [];

        foreach ($response->body->courses as $course)
        {
            $courses[] = new ModelCourse((array) $course);
        }

		return $courses;
    }
}
