<?php

namespace Suru\Docebo\DoceboApiWrapper\Api;

use Suru\Docebo\DoceboApiWrapper\Api\BaseEndpoint;
use Suru\Docebo\DoceboApiWrapper\Models\CourseMaterial;

class Organization extends BaseEndpoint {

    /**
     * Course endpoints.
     *
     * @var array
     */
    protected $endpoints = [
        'list'  => '/api/organization/listObjects',
    ];

    /**
     * @param int $course_id            Internal ID of the course
     * @param int $organization_id      Training material folder id. If not passed, returns all materials under root.
     * @param int $scormitem_id         SCORM item (folder or sco) ID
     * @param boolean $recurse_folders  By default this endpoint only returns materials at the root level
     * @param boolean $recurse_scorm    By default this endpoint only returns materials at the root level
     * @return array
     */
    public function getCourseMaterials($course_id, $organization_id = null, $scormitem_id = null, $recurse_folders = false, $recurse_scorm = false)
    {
		$headers = $this->master->getAccessTokenHeader();

		$parameters = [
            'id_course' => $course_id,
            'id_org' => $organization_id,
            'id_scormitem' => $scormitem_id
        ];

		$response = $this->master->call($this->endpoints['list'], $headers, $parameters);

        $course_materials = [];

        foreach ($response->body->objects as $course_material)
        {
            $course_materials[] = new CourseMaterial((array) $course_material);
        }

        if ($recurse_folders)
        {
            foreach ($course_materials as $course_material)
            {
                if ('folder' === $course_material->type)
                {
                    // folders may contain scorm
                    $children = $this->getCourseMaterials($course_id, $course_material->id_org, $scormitem_id, $recurse_folders, $recurse_scorm);
                    $course_material->setChildren($children);
                }
            }
        }

        if ($recurse_scorm)
        {
            foreach ($course_materials as $course_material)
            {
                if ('scorm' === $course_material->type)
                {
                    // SCORM will not contain folders (not 100% sure on this one)
                    $children = $this->getCourseMaterials($course_id, null, $course_material->id_org, $recurse_folders, $recurse_scorm);
                    $course_material->setChildren($children);
                }
            }
        }

		return $course_materials;
    }
}
